package com.dhanuka.indhan.DemoRequestActivity.activity.DemoRequestsActivity

import android.app.Activity
import android.app.DatePickerDialog
import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.AdapterView
import android.widget.ArrayAdapter
import android.widget.ListPopupWindow
import android.widget.TextView
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.NewTicketMVP.AllVillageSpinnerDialog
import com.dhanuka.indhan.Activity.NewTicketMVP.CropSpinnerDialogNew
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.CropBean
import com.dhanuka.indhan.Activity.NewTicketMVP.ProductSpinnerDialogNew
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.DataBase.TableDataModel.ProductCropResponse
import com.dhanuka.indhan.DemoRequestActivity.model.ProductListResponseNew
import com.dhanuka.indhan.DemoRequestActivity.model.ProductlistNew
import com.dhanuka.indhan.DemoRequestActivity.models.AllVillageListResponse
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.util.OnGetId
import kotlinx.android.synthetic.main.activity_demo_filter.*
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*
import kotlin.collections.ArrayList

class DemoFilterActivity : BaseActivity(), View.OnClickListener {
    var villageList: ArrayList<AllVillageListResponse.Data> = ArrayList()

    private lateinit var cropSpinnerDialog: CropSpinnerDialogNew
    private lateinit var productSpinnerDialog: ProductSpinnerDialogNew
    private val crop1RecordResponseModel = ArrayList<CropBean>()
    private var prodCropResponseModel: ArrayList<ProductCropResponse.ProductCropList> = ArrayList()
    private var productList: ArrayList<ProductlistNew> = ArrayList()
    var statusList = ArrayList<String>()
    lateinit var villagelSpinnerDialog: AllVillageSpinnerDialog

    private lateinit var preference: Preference
    var cropId = ""
    var productId = ""
    var productName = ""
    var cropName = ""
    var villageName = ""
    var status = ""
    var selectedDate = ""
    var villageId: String = ""
    var fromDate = ""
    var toDate = ""
    var apiToken = ""


    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_demo_filter)
        preference = Preference(this)
        apiToken = preference.getString("token")

        //get crop list
        updateCropList()
        //get product list
        getProductList()
        //apply click on view
        applyCLickOnView()
        getVillageList(apiToken)

        statusList.add("Pending")
        statusList.add("Excellent")
        statusList.add("Average")
        statusList.add("Poor")
        statusList.add("Overdue")
        statusList.add("Awaited")


    }

    private fun applyCLickOnView() {
        llCrop.setOnClickListener(this)
        llProduct.setOnClickListener(this)
        llVillage.setOnClickListener(this)
        llStatus.setOnClickListener(this)
        llFromDate.setOnClickListener(this)
        llToDate.setOnClickListener(this)

        btnDmSubmit.setOnClickListener(this)
    }

    /**
     * Get crop list
     */
    private fun updateCropList() {
        prodCropResponseModel = DatabaseManager.getProductCropDetails(this)
        var prpdCropListNew = DatabaseManager.getProductCropDetailProductNew(this)
        if (prpdCropListNew != null) {
            prpdCropListNew.forEach {
                var bean = CropBean()
                bean.setcID(it.cropId.toInt())
                bean.item = it.crop
                if (!checkValue(bean.getcID()!!))
                    crop1RecordResponseModel.add(bean)
            }


        }
    }

    private fun checkValue(id: Int): Boolean {
        crop1RecordResponseModel.forEach {
            if (id == it.getcID()) {
                return true
            }
        }
        return false
    }

    //get crop productListData
    private fun getProductList() {

        var model: String = preference.getString("token")
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).getProductListNew(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ProductListResponseNew>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    //listener.onFailure(e.localizedMessage ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection.")
                }

                override fun onNext(listBean: ProductListResponseNew) {
                    if (listBean.status_code == 200) {
                        productList.clear()
                        productList.addAll(listBean.data.productlist)

                    } else {
                        //listener.onFailure(listBean.message)

                    }
                }
            })

    }

    //end
    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.llCrop -> {
                cropSpinnerDialog = CropSpinnerDialogNew(
                    this,
                    crop1RecordResponseModel,
                    "Select or Search Crop",
                    object : OnGetId {
                        override fun onClick(item: String, position: Int, cropID: String) {
                            txtCrop.text = item
                            cropId = cropID
                            cropName = item
                        }
                    }

                )
                cropSpinnerDialog.showSpinerDialog()
            }
            R.id.llProduct -> {
                if (productList.size > 0) {
                    productSpinnerDialog = ProductSpinnerDialogNew(
                        this,
                        productList,
                        "Select or Search Product",
                        object : OnGetId {
                            override fun onClick(item: String, position: Int, productID: String) {
                                txtProduct.text = item
                                productId = productID
                                productName = item
                            }

                        })
                    productSpinnerDialog.showSpinerDialog()
                }

            }
            R.id.llVillage -> {
                if (villageList.size > 0) {
                    villagelSpinnerDialog = AllVillageSpinnerDialog(
                        this,
                        villageList,
                        "Select or Search Village",
                        object : OnGetId {
                            override fun onClick(item: String, position: Int, sId: String) {
                                villageId = sId
                                txtVillageName.text = item

                            }

                        })
                    villagelSpinnerDialog.showSpinerDialog()
                }
            }

            R.id.llFromDate -> {
                showDate(txtFromDate)
            }
            R.id.llToDate -> {
                showDate(txtToDate)
            }
            R.id.llStatus -> {
                showListPopUpWindowForStatus()
            }
            R.id.btnDmSubmit -> {
                var intent = Intent()
                intent.putExtra(Constants.PRODUCT_NAME, productName)
                intent.putExtra(Constants.CROP_NAME, cropName)
                intent.putExtra(Constants.PRODUCT_ID, productId)
                intent.putExtra(Constants.CROP_ID, cropId)
                intent.putExtra(Constants.VILLAGE_ID, villageId)
                intent.putExtra(Constants.STATUS, status)
                intent.putExtra(Constants.FROM_DATE, fromDate)
                intent.putExtra(Constants.TO_DATE, toDate)

                intent.putExtra(Constants.FROM, "filter")
                setResult(Activity.RESULT_OK, intent)
                finish()


            }


        }
    }

    fun validateForm(): Boolean {
        cropName = txtCrop.text.toString()
        productName = txtProduct.text.toString()
        villageName = txtVillageName.text.toString()
        status = txtStatus.text.toString()
        fromDate = txtFromDate.text.toString()
        toDate = txtToDate.text.toString()
        if (cropName.equals("")) {
            makeToast("Please select crop name")
            return false
        } else if (productName.equals("")) {
            makeToast("please select product name")
            return false
        } else if (villageName.equals("")) {
            makeToast("Please select village name")
            return false
        } else if (status.equals("")) {
            makeToast("please select status")
            return false
        } else if (fromDate.equals("")) {
            makeToast("please select from date")
            return false
        } else if (toDate.equals("")) {
            makeToast("please select to date")
            return false
        }


        return true
    }

    /*
   show date
    */
    private fun showDate(txtFromDate: TextView): Unit {
        val c = Calendar.getInstance( Locale.ENGLISH)
        val year = c.get(Calendar.YEAR)
        val month = c.get(Calendar.MONTH)
        val day = c.get(Calendar.DAY_OF_MONTH)

        val mDatePicker = DatePickerDialog(
            activity,
            DatePickerDialog.OnDateSetListener { view, year, monthOfYear, dayOfMonth ->

                // Display Selected date in textbox
                //lblDate.setText("" + dayOfMonth + " " + MONTHS[monthOfYear] + ", " + year)
                selectedDate = "" + year + "-" + (monthOfYear + 1) + "-" + dayOfMonth
                txtFromDate.text = selectedDate

            },
            year,
            month,
            day
        )
        //  mDatePicker.getDatePicker().setMaxDate(System.currentTimeMillis());
        mDatePicker.getDatePicker().setMaxDate(System.currentTimeMillis());

        // Subtract 3 months from Calendar updated date
        c.add(Calendar.MONTH, -6);
        // Set the Calendar new date as minimum date of date picker
        mDatePicker.getDatePicker().setMinDate(c.getTimeInMillis());
        mDatePicker.show()
    }

    //end
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
                }

                override fun onNext(villageBean: AllVillageListResponse) {
                    hideProgress()
                    if (!villageBean.data.isNullOrEmpty()) {
                        villageList.clear()
                        villageList.addAll(villageBean.data)
                    }

                }
            }
            )
    }

    private fun showListPopUpWindowForStatus() {
        val adapter = ArrayAdapter<String>(this, android.R.layout.simple_list_item_1, statusList)
        var listPopUpWindow: ListPopupWindow = ListPopupWindow(this)
        listPopUpWindow.setOnItemClickListener(AdapterView.OnItemClickListener { parent, view, position, id ->
            txtStatus.text = statusList.get(position)
            listPopUpWindow.dismiss()
        })
        listPopUpWindow.anchorView = llStatus
        listPopUpWindow.setAdapter(adapter)
        listPopUpWindow.show()
    }
}
