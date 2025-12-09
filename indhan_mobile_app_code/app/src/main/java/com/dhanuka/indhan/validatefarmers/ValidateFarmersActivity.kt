package com.dhanuka.indhan.validatefarmers

import android.app.Dialog
import android.content.Intent
import android.graphics.Color
import android.graphics.drawable.ColorDrawable
import android.net.Uri
import android.os.Bundle
import android.view.View
import android.view.Window
import android.widget.ArrayAdapter
import android.widget.ListPopupWindow
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.ATPMenu.activity.supervisorAtp.fragment.PaginationListener
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.RegisterFarmersMVP.Model.IfcFarmersListResponse
import com.dhanuka.indhan.DemoRequestActivity.model.ProductListResponseNew
import com.dhanuka.indhan.DemoRequestActivity.model.ProductlistNew
import com.dhanuka.indhan.Fonts.Medium
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.RegisterFarmerScreen.ifcFarmerRegistration.model.AddIfcFarmer
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.activity_ticket_history.*
import kotlinx.android.synthetic.main.activity_validate_farmers.*
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.GlobalScope
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import org.greenrobot.eventbus.EventBus
import rx.Single
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*


class ValidateFarmersActivity : BaseActivity(), ValidateFarmerContractor.ValidateFarmerView {

    private var type: String = ""
    private var baseUrl: String = ""
    private var appDatabase: AppDatabase? = null
    lateinit var mPresenter: ValidateFarmerContractor.ValidateFarmerPresenter
    private lateinit var preference: Preference
    private var adapter: ValidateFarmerAdapter? = null
    private var ifcFarmerAdapter: ValidateIfcFarmerAdapter? = null
    private var mPosition: Int = 0
    private var productList: ArrayList<ProductlistNew> = ArrayList()
    private var ifcFarmerDataList: ArrayList<IfcFarmersListResponse.Data> = ArrayList()
    private var validatedFarmerBean: ArrayList<ValidatedFarmerBean.Data> = ArrayList()
    private var currentPage = PaginationListener.PAGE_START
    private var mIsLastPage = false
    private var mIsLoading = false
    private lateinit var linearLayout: LinearLayoutManager
    var offset = 0
    private var totalPage = 50

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_validate_farmers)
        mPresenter = ValidatefarmerPresenter(this)
        preference = Preference(this)
        appDatabase = DatabaseClient.getInstance(this).appDatabase
        pd_name.text = intent.getStringExtra("data")
        type = intent.getStringExtra("key") ?: ""
        val ticketNumber = intent.getStringExtra("ticket_number")

        /*
         *get data and send to Api
         * */
        if (type.equals("ifc_farmer", ignoreCase = true)) {
            val query = HashMap<String, Any>()
            query["ticket_no"] = ticketNumber!!
            query["apiToken"] = preference.getString("token")
            if (CheckConnectionStatus.isInternetAvailable(this)) {
                showProgress()
                callApiGetIfcFarmers(query)
            } else {
                makeToast(resources.getString(R.string.no_internet_connection))
                getRegisteredIfcFarmerFromDB(ticketNumber)
            }
        } else {
            val query = HashMap<String, Any>()
            query["plan_atp_id"] = intent?.getStringExtra("atpId") ?: ""
            query["type"] = type
            query["apiToken"] = preference.getString("token")
            query["offset"] = offset
            if (CheckConnectionStatus.isInternetAvailable(this)) {
                mPresenter.getFarmerList(query)
            } else {
                makeToast(resources.getString(R.string.no_internet_connection))
                if (intent.getStringExtra("key") == "total_farmer")
                    if (ticketNumber != null) {
                        getRegisteredFarmerFromDB(ticketNumber)
                    }
            }
            setView()
            recyclerViewTestimonial.addOnScrollListener(object : PaginationListener(linearLayout) {
                override fun isLastPage(): Boolean {
                    return mIsLastPage
                }

                override fun loadMoreItems() {
                    if (type.equals("ifc_farmer", ignoreCase = true)) {
                    } else {
                        mIsLoading = true
                        currentPage++
                        val queryOne = HashMap<String, Any>()
                        queryOne["plan_atp_id"] = intent?.getStringExtra("atpId") ?: ""
                        queryOne["type"] = intent?.getStringExtra("key") ?: ""
                        queryOne["apiToken"] = preference.getString("token")
                        queryOne["offset"] = offset
                        mPresenter.getFarmerList(queryOne)
                    }
                }

                override fun isLoading(): Boolean {
                    return mIsLoading
                }
            })
        }

        getProductList()
    }

    private fun getRegisteredIfcFarmerFromDB(ticketNumber: String) {
        rx.Observable.fromCallable {
            (appDatabase?.ifcFarmerDao()
                ?.getAllIfcFarmerData(0, ticketNumber) ?: ArrayList<AddIfcFarmer>()) as ArrayList<AddIfcFarmer>
        }.subscribeOn(Schedulers.io())
            .observeOn(AndroidSchedulers.mainThread()).subscribe {
                it?.forEach() { farmer ->
                    val data = IfcFarmersListResponse.Data()
                    data.acreage = farmer.acrers
                    data.user_type = "1"
                    data.id = farmer.id
                    data.user_id = farmer.id
                    data.updated_at = farmer.serverTime
                    data.created_at = farmer.serverTime
                    data.status = farmer.status
                    data.latitude = farmer.latitude.toString()
                    data.longitude = farmer.longitude.toString()
                    data.image_date_time = farmer.image_date_time
                    data.image = farmer.image
                    data.mobile = farmer.mobile
                    data.farmer_name = farmer.farmer_name
                    data.ticket_no = ticketNumber
                    ifcFarmerDataList.add(data)
                    if (ifcFarmerAdapter == null) {
                        setIfcAdapter(baseUrl)
                    } else {
                        ifcFarmerAdapter?.notifyDataSetChanged()
                    }
                }
            }
    }

    private fun callApiGetIfcFarmers(query: HashMap<String, Any>) {
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).getIfcFarmerList(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<IfcFarmersListResponse>() {
                override fun onCompleted() {
                    hideProgress()
                }

                override fun onError(e: Throwable) {
                    hideProgress()
                }

                override fun onNext(farmersData: IfcFarmersListResponse) {
                    hideProgress()
                    if (farmersData.status == "success" && farmersData.status_code == 200) {
                        ifcFarmerDataList.clear()
                        ifcFarmerDataList.addAll(farmersData.data)
                        setIfcAdapter(baseUrl)
                        baseUrl = farmersData.base_url
                    } else if (farmersData.status_code == 409) {
                        EventBus.getDefault().post("logout")
                    }
                }
            })
    }

    private fun setIfcAdapter(baseUrl: String) {
        ifcFarmerAdapter =
            ValidateIfcFarmerAdapter(this, object : IfcValidationListener {
                override fun onIfcCall(position: Int) {
                    mPosition = position
                    val query = HashMap<String, Any>()
                    query["farmerId"] = ifcFarmerDataList[position].id
                    query["status"] = "1"
                    query["farmer_type"] = "ifc_farmer"
                    query["apiToken"] = preference.getString("token")
                    if (CheckConnectionStatus.isInternetAvailable(this@ValidateFarmersActivity)) {
                        mPresenter.onCallFarmer(query)
                    } else {
                        makeToast(resources.getString(R.string.no_internet_connection))
                    }
                }

                override fun onChangeIfcStatus(position: Int) {
                    mPosition = position
                    showdialog(position, "ifc_farmer")
                }

            }, ifcFarmerDataList, baseUrl)
        linearLayout = LinearLayoutManager(this)
        recyclerViewTestimonial.layoutManager = linearLayout
        recyclerViewTestimonial.adapter = ifcFarmerAdapter
    }

    private fun getRegisteredFarmerFromDB(ticketNumber: String) {
        GlobalScope.launch {
            val farmerList = ArrayList<ValidatedFarmerBean.Data>()
            withContext(Dispatchers.IO) {
                val list = appDatabase?.farmerRegistrationDao()?.getAllNumberList(ticketNumber, 0) ?: ArrayList()
                for (i in list.indices) {
                    val data = ValidatedFarmerBean.Data("NA", "0", list[i].mobileNumber, "0", "0")
                    farmerList.add(data)
                }
            }.run {
                withContext(Dispatchers.Main) {
                    setAdapter(farmerList)
                }
            }
        }
        Single.fromCallable {

        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    private fun setAdapter(items: ArrayList<ValidatedFarmerBean.Data>) {
        if (items.size > 0) {
            if (items.size > 0) {
                if (currentPage < totalPage) {
                    validatedFarmerBean.addAll(items)
                    txtNoItems.visibility = View.GONE
                } else {
                    mIsLastPage = true
                }
                mIsLoading = false
                offset++
            }
            adapter?.notifyDataSetChanged()
        } else {
            txtNoItems.visibility = View.VISIBLE
        }
    }

    /*
   * set adpete
   * */
    private fun setView() {
        adapter = ValidateFarmerAdapter(this, validatedFarmerBean, object : ValidationListener {
            override fun onCall(position: Int) {
                mPosition = position
                val query = HashMap<String, Any>()
                query["farmerId"] = validatedFarmerBean[position].registeredFarmerId
                query["status"] = "1"
                query["farmer_type"] = "register_farmer"
                query["apiToken"] = preference.getString("token")
                if (CheckConnectionStatus.isInternetAvailable(this@ValidateFarmersActivity)) {
                    mPresenter.onCallFarmer(query)
                } else {
                    makeToast(resources.getString(R.string.no_internet_connection))
                }
            }

            override fun onChanageStatus(position: Int) {
                mPosition = position
                showdialog(position, "general")

            }
        })
        linearLayout = LinearLayoutManager(this)
        recyclerViewTestimonial.layoutManager = linearLayout
        recyclerViewTestimonial.adapter = adapter

    }

    private fun showdialog(postion: Int, from: String) {
        val dialog = Dialog(this)
        dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_change_status)
        dialog.setCanceledOnTouchOutside(false)
        dialog.setCancelable(false)
        val tvOkay = dialog.findViewById<View>(R.id.tvMeOkay) as Medium
        val tvNo = dialog.findViewById<View>(R.id.tvMeCancel) as Medium

        tvNo.setOnClickListener {
            dialog.dismiss()
        }

        tvOkay.setOnClickListener {
            showdialogTwo(postion, from)
            dialog.dismiss()
        }
        dialog.show()
    }

    private fun showdialogTwo(postion: Int, from: String) {
        var dateData: String = ""
        var product: String = ""
        val dialog = Dialog(this)
        dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_choose_product)
        dialog.setCanceledOnTouchOutside(false)
        dialog.setCancelable(false)
        val txtProduct = dialog.findViewById<View>(R.id.txtProduct) as Medium
        val txtDate = dialog.findViewById<View>(R.id.txtDate) as Medium
        val list = mutableListOf<String>()

        for (i in 0 until productList.size) {
            list.add(productList[i].product)
        }

        txtProduct.setOnClickListener {
            val listPopupAdapter = ArrayAdapter(
                this@ValidateFarmersActivity,
                android.R.layout.simple_spinner_dropdown_item,
                list
            )
            val popupWindow = ListPopupWindow(this@ValidateFarmersActivity)
            popupWindow.setOnItemClickListener { parent, view, position, id ->
                product = productList[position].product_id
                txtProduct.text = productList[position].product
                popupWindow.dismiss()
            }

            popupWindow.setContentWidth(txtProduct.width)
            popupWindow.height = ListPopupWindow.WRAP_CONTENT
            popupWindow.anchorView = txtProduct
            popupWindow.setAdapter(listPopupAdapter)
            popupWindow.show()
        }


        txtDate.setOnClickListener {
            Utility.getPickedFutureDate(
                this@ValidateFarmersActivity,
                object : Utility.DatePickCallBack {
                    override fun onDateSet(date: String?, formattedDate: String?) {
                        txtDate.text = date
                        dateData = date!!
                    }

                    override fun onCancel() {

                    }
                })
        }


        val tvOkay = dialog.findViewById<View>(R.id.tvMeSubmit) as Medium
        val tvNo = dialog.findViewById<View>(R.id.tvMeCancel) as Medium

        tvNo.setOnClickListener {
            dialog.dismiss()
        }

        tvOkay.setOnClickListener {
            val query = HashMap<String, Any>()
            query["product_id"] = product
            query["expected_purchase_date"] = dateData
            query["status"] = "1"
            query["apiToken"] = preference.getString("token")
            if (from.equals("ifc_farmer", ignoreCase = true)) {
                query["farmerId"] = ifcFarmerDataList[postion].id
                query["farmer_type"] = "ifc_farmer"
            } else {
                query["farmerId"] = validatedFarmerBean[postion].registeredFarmerId
                query["farmer_type"] = "register_farmer"
            }
            if (CheckConnectionStatus.isInternetAvailable(this@ValidateFarmersActivity)) {
                mPresenter.onMakeLead(query)
            } else {
                makeToast(resources.getString(R.string.no_internet_connection))
            }
            dialog.dismiss()

        }
        dialog.show()
    }

    //get crop productListData
    private fun getProductList() {
        val model: String = preference.getString("token")
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).getProductListNew(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ProductListResponseNew>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {}

                override fun onNext(listBean: ProductListResponseNew) {
                    if (listBean.status_code == 200) {
                        productList.clear()
                        productList.addAll(listBean.data.productlist)
                    }
                }
            })
    }

    override fun setCall(items: SuccessBean) {
        if (type.equals("ifc_farmer", ignoreCase = true)) {
            val intent = Intent(Intent.ACTION_DIAL)
            intent.data = Uri.parse("tel: ${ifcFarmerDataList[mPosition].mobile}")
            startActivity(intent)
        } else {
            validatedFarmerBean[mPosition].callStatus = "1"
            adapter?.notifyDataSetChanged()
            val intent = Intent(Intent.ACTION_DIAL)
            intent.data = Uri.parse("tel: ${validatedFarmerBean[mPosition].mobile}")
            startActivity(intent)
        }

    }

    override fun setChangeStatus(items: SuccessBean) {
        if (type.equals("ifc_farmer", ignoreCase = true)) {
            makeToast("Lead Saved Successfully.")
        } else {
            validatedFarmerBean[mPosition].leadStatus = "1"
            adapter?.notifyDataSetChanged()
            makeToast(items.message)
        }

    }

    override fun onClick(v: View?) {}

    override fun setFarmerList(items: ValidatedFarmerBean) {
        if (items.data.size > 0) {
            if (items.data.size > 0) {
                if (currentPage < totalPage) {
                    validatedFarmerBean.addAll(items.data)
                    txtNoItems.visibility = View.GONE
                } else {
                    mIsLastPage = true
                }
                mIsLoading = false
                offset++
            }
            adapter?.notifyDataSetChanged()
        } else {
            txtNoItems.visibility = View.VISIBLE
        }

    }

    override fun failure(items: String) {
        makeToast(items)
    }
}
