package com.dhanuka.indhan.DemoRequestActivity.activity.DemoRequestsActivity

import android.app.Activity
import android.app.Dialog
import android.content.Intent
import android.os.Bundle
import android.view.View
import android.view.ViewGroup
import android.view.WindowManager
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.NewTicketMVP.AllVillageSpinnerDialog
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DemoRequestActivity.activity.CreateRequestActivity.SelectExistingFarmerActivity
import com.dhanuka.indhan.DemoRequestActivity.adapters.DemoRequestAdapter
import com.dhanuka.indhan.DemoRequestActivity.dialog.SelectFarmerDialog
import com.dhanuka.indhan.DemoRequestActivity.models.AllVillageListResponse
import com.dhanuka.indhan.DemoRequestActivity.models.DemoRequestData
import com.dhanuka.indhan.DemoRequestActivity.models.DemoRequestListResponse
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.util.OnGetId
import com.dhanuka.indhan.util.OnSuccess
import kotlinx.android.synthetic.main.activity_demo_requests.*
import kotlinx.android.synthetic.main.dialog_ddvillage_list.*
import org.greenrobot.eventbus.EventBus
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers

class DemoRequestsActivity : BaseActivity() {
    private lateinit var preference: Preference
    private var demoRequestList: ArrayList<DemoRequestData> = ArrayList()
    var productId = ""
    var cropId = ""
    var from = ""//it check from where we are coming
    var villageList: ArrayList<AllVillageListResponse.Data> = ArrayList()
    var apiToken: String = ""
    var villageId: String = ""
    var status = ""
    var fromDate = ""
    var toDate = ""

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_demo_requests)
        preference = Preference(this)
        apiToken = preference.getString("token")
        //apply click on view
        applyClickOnView()
        btnOfflineDemoRequests.setOnClickListener {
            startActivity(Intent(this, OfflineDemoRequestsActivity::class.java))
        }
    }

    override fun onResume() {
        super.onResume()
        if (from != "filter") {
            villageId = ""
            //get demo request list Api
            getDemoRequestListApi("", "")
        }
    }

    private fun applyClickOnView() {
        llCreateDemoRequest.setOnClickListener {
            //for black so that coming back it run like starting
            from = ""
            cropId = ""
            productId = ""
            SelectFarmerDialog(this, object : OnSuccess {
                override fun onClick(data: String) {
                    if (data.equals("yes", ignoreCase = true)) {
                        getVillageList(apiToken)
                    }
                }
            }).show()
        }
        imgFilter.setOnClickListener(this)

    }

    //get demo request list
    private fun getDemoRequestListApi(productId: String, cropId: String) {
        showProgress()
        val model: String = preference.getString("token")
        val query = java.util.HashMap<String, Any>()
        query.put("apiToken", model)
        query.put("approval", "")
        query.put("crop", cropId)
        query.put("product", productId)
        query.put("village_id", villageId)
        query.put("status", status)
        query.put("from_date", fromDate)
        query.put("to_date", toDate)

        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).demoRequestList(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<DemoRequestListResponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                    makeToast(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onNext(demoBean: DemoRequestListResponse) {
                    hideProgress()
                    if (demoBean != null) {
                        if (demoBean.status_code == 409) {
                            EventBus.getDefault().post("logout")
                            return
                        }
                    }
                    if (demoBean.data.isNotEmpty()) {
                        demoRequestList.clear()
                        txtNoDataFound.visibility = View.GONE

                        demoRequestList.addAll(demoBean.data)
                        //set data on adapter
                        setDataOnAdapter(demoRequestList)
                    } else {
                        txtNoDataFound.visibility = View.VISIBLE
                        demoRequestList.clear()
                        setDataOnAdapter(demoRequestList)
                    }
                }
            })
            //end
    }

    //function for adapter
    private fun setDataOnAdapter(demoRequestList: ArrayList<DemoRequestData>) {
        // Creates a vertical Layout Manager
        rvDemoRequest.layoutManager =
            LinearLayoutManager(this@DemoRequestsActivity)
        val adapter = DemoRequestAdapter(this@DemoRequestsActivity, demoRequestList)
        rvDemoRequest.adapter = adapter
    }

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.imgFilter -> {
                val intent = Intent(this@DemoRequestsActivity, DemoFilterActivity::class.java)
                startActivityForResult(intent, 100)
            }
        }


    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == 100) {
            if (resultCode == Activity.RESULT_OK) {
                data?.let {
                    if (it.hasExtra(Constants.FROM)) {
                        from = it.getStringExtra(Constants.FROM) ?: ""
                    }
                    if (it.hasExtra(Constants.PRODUCT_ID)) {
                        productId = it.getStringExtra(Constants.PRODUCT_ID) ?: ""
                    }
                    if (it.hasExtra(Constants.CROP_ID)) {
                        cropId = it.getStringExtra(Constants.CROP_ID) ?: ""

                    }
                    if (it.hasExtra(Constants.VILLAGE_ID)) {
                        villageId = it.getStringExtra(Constants.VILLAGE_ID) ?: ""
                    }
                    if (it.hasExtra(Constants.STATUS)) {
                        status = it.getStringExtra(Constants.STATUS) ?: ""
                    }
                    if (it.hasExtra(Constants.FROM_DATE)) {
                        fromDate = it.getStringExtra(Constants.FROM_DATE) ?: ""
                    }
                    if (it.hasExtra(Constants.TO_DATE)) {
                        toDate = it.getStringExtra(Constants.TO_DATE) ?: ""
                    }
                    getDemoRequestListApi(productId, cropId)
                }
            }
        }
    }

    //show dialog
    fun showDdVillageListDialog(): Unit {
        val dialog = Dialog(this)
        dialog.window!!.setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN)
        dialog.window!!.attributes.windowAnimations = R.style.PauseDialogAnimation
        dialog.setContentView(R.layout.dialog_ddvillage_list)
        dialog.window!!.setLayout(
            ViewGroup.LayoutParams.MATCH_PARENT,
            ViewGroup.LayoutParams.WRAP_CONTENT
        )

        dialog.window!!.setBackgroundDrawableResource(android.R.color.transparent)
        var villagelSpinnerDialog: AllVillageSpinnerDialog

        dialog.llVillage.setOnClickListener {
            if (villageList.size > 0) {
                villagelSpinnerDialog = AllVillageSpinnerDialog(
                    this,
                    villageList,
                    "Select or Search Village",
                    object : OnGetId {
                        override fun onClick(item: String, position: Int, sId: String) {
                            villageId = sId
                            dialog.tvVillageName.text = item

                        }

                    })
                villagelSpinnerDialog.showSpinerDialog()
            }
        }

        dialog.buttonSubmit.setOnClickListener {
            val getVillage = dialog.tvVillageName.text.toString()
            if (!getVillage.equals("")) {
                val intent =
                    Intent(this@DemoRequestsActivity, SelectExistingFarmerActivity::class.java)
                intent.putExtra(Constants.VILLAGE_ID, villageId)
                startActivity(intent)
                dialog.dismiss()
            } else {
                makeToast("Please select village name")
            }

        }

        dialog.show()
    }

    //show dialog
    fun showCloseAtpDialog() {
        val dialog = Dialog(this)
        dialog.window!!.setSoftInputMode(WindowManager.LayoutParams.SOFT_INPUT_STATE_ALWAYS_HIDDEN)
        dialog.window!!.attributes.windowAnimations = R.style.PauseDialogAnimation
        dialog.setContentView(R.layout.dialog_ddvillage_list)
        dialog.window!!.setLayout(
            ViewGroup.LayoutParams.MATCH_PARENT,
            ViewGroup.LayoutParams.WRAP_CONTENT
        )

        dialog.window!!.setBackgroundDrawableResource(android.R.color.transparent)

        dialog.buttonSubmit.setOnClickListener {
            makeToast("ok")
        }


        dialog.show()
    }

    //get crop villageList
    private fun getVillageList(token: String) {
        showProgress()
        val query = java.util.HashMap<String, Any>()
        // query.put("ECN", ecn)
        query.put("apiToken", token)
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).getDdVillageList(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<AllVillageListResponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                    makeToast(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onNext(villageBean: AllVillageListResponse) {
                    hideProgress()
                    if (!villageBean.data.isNullOrEmpty()) {
                        villageList.clear()
                        villageList.addAll(villageBean.data)
                        showDdVillageListDialog()

                    }

                }
            }
            )
    }
    //end
}
