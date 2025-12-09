package com.dhanuka.indhan.ATPMenu.activity

import android.app.Activity
import android.app.Dialog
import android.content.Intent
import android.graphics.Color
import android.graphics.drawable.ColorDrawable
import android.os.Bundle
import android.view.View
import android.view.Window
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.ATPMenu.activity.supervisorAtp.SupervisorATPFilterActivity
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.Fonts.Medium
import com.dhanuka.indhan.Fonts.RegularEditText
import com.dhanuka.indhan.PlanSyllabusForBdo.Adapter.ATPMenuAdapter
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.SelfParticipationActivity.Model.ParticipationPlannedResponse
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.Utility
import com.google.firebase.analytics.FirebaseAnalytics
import kotlinx.android.synthetic.main.activity_atp_menu.*
import rx.Single
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*

//        all, These are the three types of FL :
//        1. BDO-FL
//        2. SALES-FL
//        3.COUNTERPART-FL
class AtpMenuActivity : BaseActivity(), ATPMenuContractor.AddFarmerView,
    ATPMenuAdapter.ActivateTicketClickListener {
    override fun onSelectedActivate(model: ATPBean.Data) {

    }

    override fun setClosedATP(items: ClosedATPBean) {
        makeToast(items.message)
    }


    override fun onClick(v: View?) {

    }

    override fun setItems(items: ATPBean) {
        model = items
        if (model.data.size > 0) {
            insertATPListInDb(model.data)
            txtEmpty.visibility = View.GONE
        } else {
            txtEmpty.visibility = View.VISIBLE
        }

        menuAdapter = ATPMenuAdapter(model.data, this, object : OnClickListener {
            override fun onClick(position: Int) {
                if (designation == "BDO-FL" || designation == "SALES-FL") {
                    showdialog(position)
                }
            }
        })
        recyclerView.adapter = menuAdapter
    }

    override fun failure(items: String) {
        makeToast(items)
        logEventCustom(items, "v2/atpList or v2/closeAtp")
    }

    private fun logEventCustom(mes: String?, apiName: String) {
        val bundle = HashMap<String, Any>()
        bundle[FirebaseAnalytics.Param.ITEM_ID] = preference.getString("user_id")
        bundle["Api_Name"] = apiName
        if (mes != null) {
            bundle["error"] = mes
        } else {
            bundle["error"] = "unknown error occurred"
        }
        bundle["user_detail"] = preference.getString("user_id")
            .plus("_${preference.getString("ecn")}")
            .plus("_${preference.getString("user_type")}")
            .plus(
                "_${preference.getString("Username")}"
                    .plus("_${preference.getString("designation")}")
            )
        Utility.logEvent("AtpMenuActivity", "failed_api_$apiName", bundle)
    }

    private lateinit var mPresenter: ATPMenuContractor.ATPMenuPresenter
    private lateinit var preference: Preference
    private lateinit var menuAdapter: ATPMenuAdapter
    private lateinit var model: ATPBean
    private var designation = ""
    private lateinit var databaseClient: AppDatabase

    var cropId = ""
    var productId = ""
    var productName = ""
    var cropName = ""
    var fromDate = ""
    var toDate = ""
    var DDID = ""

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_atp_menu)

        imgFilter.setOnClickListener {
            val i = Intent(this, SupervisorATPFilterActivity::class.java)
            i.putExtra("from", "ATP")
            startActivityForResult(i, 100)
        }
        databaseClient = DatabaseClient.getInstance(this).appDatabase
        recyclerView.layoutManager =
            LinearLayoutManager(this)
    }

    override fun onResume() {
        super.onResume()

        mPresenter = ATPMenuPresenter(this)
        preference = Preference(this)
        designation = preference.getString("designation")
        val query = HashMap<String, Any>()
        query["apiToken"] = preference.getString("token")
        query["from_date"] = fromDate
        query["to_date"] = toDate
        query["product"] = productId
        query["crop"] = cropId
        query["dd_ids"] = DDID
        if (!(this as Activity).isFinishing) {
            if (CheckConnectionStatus.isInternetAvailable(this)) {
                mPresenter.onATPMenu(query)
            } else {
                makeToast(resources.getString(R.string.no_internet_connection))
                getLocalATPList()
            }
        }
    }

    private fun showdialog(postion: Int) {
        val dialog = Dialog(this)
        dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_close_atp)
        dialog.setCanceledOnTouchOutside(false)
        dialog.setCancelable(false)
        val msg = dialog.findViewById<View>(R.id.msg) as Medium
        val tvOkay = dialog.findViewById<View>(R.id.tvMeOkay) as Medium
        val tvNo = dialog.findViewById<View>(R.id.tvMeCancel) as Medium
        val edtTextField = dialog.findViewById<View>(R.id.edtTextField) as RegularEditText

        tvNo.setOnClickListener {
            dialog.dismiss()
        }

        tvOkay.setOnClickListener {
            if (edtTextField.text.toString() != "") {
                val query = HashMap<String, Any>()
                query["apiToken"] = preference.getString("token")
                query["atp_id"] = model.data[postion].plan_atp_id.toString()
                query["reason"] = edtTextField.text.toString()
                if (CheckConnectionStatus.isInternetAvailable(this)) {
                    reasonAPI(query)
                } else {
                    closeTicketOffline(
                        edtTextField.text.toString(),
                        model.data[postion].plan_atp_id.toString()
                    )
                    makeToast("No Internet Connection. Data will be synced when online.")
                }
            } else {
                makeToast("Please Enter Reason for Close")
            }
            dialog.dismiss()
        }
        dialog.show()
    }

    private fun closeTicketOffline(reason: String, atpId: String) {
        Single.fromCallable {
            databaseClient.atpListDao().updateReason(atpId, reason)
            databaseClient.atpListDao().updateStatus(atpId, "2")
            databaseClient.atpListDao().updateTicketClosed(atpId, true)
            getLocalATPList()
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    private fun reasonAPI(model: HashMap<String, Any>) {
        showProgress()
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).activateTicketReason(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ParticipationPlannedResponse>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    hideProgress()
                    makeToast("Network error")
                }

                override fun onNext(loginBean: ParticipationPlannedResponse) {
                    hideProgress()
                    if (loginBean.statusCode == 200) {
                        makeToast(loginBean.message)
                        val query = HashMap<String, Any>()
                        query["apiToken"] = preference.getString("token")
                        if (CheckConnectionStatus.isInternetAvailable(this@AtpMenuActivity)) {
                            mPresenter.onATPMenu(query)
                        } else {
                            makeToast(resources.getString(R.string.no_internet_connection))
                        }

                    } else {
                        makeToast(loginBean.message)

                    }
                }
            })
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == 100 && resultCode == Activity.RESULT_OK) {
            cropId = data?.getStringExtra(Constants.CROP_ID) ?: ""
            productId = data?.getStringExtra(Constants.PRODUCT_ID) ?: ""
            fromDate = data?.getStringExtra(Constants.FROM_DATE) ?: ""
            toDate = data?.getStringExtra(Constants.TO_DATE) ?: ""
            DDID = data?.getStringExtra(Constants.PRODUCT_NAME) ?: ""
        }
    }

    private fun insertATPListInDb(data: List<ATPBean.Data>) {
        Single.fromCallable {
            databaseClient.atpListDao().deleteTableData()
            databaseClient.atpFarmerDao().deleteTableData()
            databaseClient.atpCropDao().deleteTableData()
            databaseClient.atpProductDao().deleteTableData()
            for (i in data.indices) {
                val rowId = databaseClient.atpListDao()?.insert(data[i])
                for (j in data[i].crop.indices) {
                    val cropData = data[i].crop[j]
                    val crop = ATPBean.Data.Crop()
                    crop.aptId = rowId!!.toInt()
                    crop.crop = cropData.crop
                    crop.crop_id = cropData.crop_id
                    databaseClient.atpCropDao().insert(crop)
                }
                for (j in data[i].product.indices) {
                    val cropData = data[i].product[j]
                    val product = ATPBean.Data.Product()
                    product.aptId = rowId!!.toInt()
                    product.product = cropData.product
                    product.product_id = cropData.product_id
                    databaseClient.atpProductDao().insert(product)
                }
                if (data[i].invitedFarmerlist.isNotEmpty())
                    for (j in data[i].invitedFarmerlist.indices)
                        databaseClient.atpFarmerDao().insert(data[i].invitedFarmerlist[j])
            }
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    private fun getLocalATPList() {
        Single.fromCallable {
            val list = databaseClient.atpListDao().allATPList
            for (i in list.indices) {
                list[i].crop = databaseClient.atpCropDao()
                    .getAllCropList(list[i].id) as ArrayList<ATPBean.Data.Crop>?
                list[i].product = databaseClient.atpProductDao().getAllProductList(list[i].id)
            }
            val atpBean = ATPBean()
            atpBean.data = list as ArrayList<ATPBean.Data>
            atpBean.message = ""
            atpBean.status = "200"
            atpBean.status_code = 200
            runOnUiThread { setAdapter(atpBean) }
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    private fun setAdapter(items: ATPBean) {
        model = items
        if (model.data.size > 0) {
            txtEmpty.visibility = View.GONE
        } else {
            txtEmpty.visibility = View.VISIBLE

        }
        menuAdapter = ATPMenuAdapter(model.data, this, object : OnClickListener {
            override fun onClick(position: Int) {
                if (designation == "BDO-FL" || designation == "SALES-FL") {
                    showdialog(position)
                }
            }
        })
        recyclerView.adapter = menuAdapter
    }
}
