package com.dhanuka.indhan.MyLeadMenu.activities.myleadactivity

import android.app.Activity
import android.app.DatePickerDialog
import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.TextView
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.NewTicketMVP.CropProductSpinnerDialog
import com.dhanuka.indhan.Activity.NewTicketMVP.CropSpinnerDialogNew
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.CropBean
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.DataBase.TableDataModel.ProductCropResponse
import com.dhanuka.indhan.DemoRequestActivity.models.CropProductData
import com.dhanuka.indhan.DemoRequestActivity.models.CropProductList
import com.dhanuka.indhan.DemoRequestEvaluated.adapters.SelectDdAdapter
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.acpfarmers.ACPFIlterBean
import com.dhanuka.indhan.activities.FLBean
import com.dhanuka.indhan.bdoddlist.BdoDdListBean
import com.dhanuka.indhan.teamsummary.models.TeamSummaryDdDetail
import com.dhanuka.indhan.util.OnGetId
import com.google.gson.Gson
import kotlinx.android.synthetic.main.activity_my_lead_filter.*
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*
import kotlin.collections.ArrayList

class MyLeadFilterActivity : BaseActivity() {
    var ddDetailList: ArrayList<TeamSummaryDdDetail> = ArrayList()
    private lateinit var preference: Preference
    var ddList: ArrayList<BdoDdListBean.Data> = ArrayList()
    var list: ArrayList<String> = ArrayList()
    private val crop1RecordResponseModel = java.util.ArrayList<CropBean>()
    private var prodCropResponseModel: ArrayList<ProductCropResponse.ProductCropList> =
        java.util.ArrayList()
    private lateinit var cropSpinnerDialog: CropSpinnerDialogNew
    private var cropProductList: ArrayList<CropProductData> = ArrayList()
    private lateinit var cropProductSpinnerDialog: CropProductSpinnerDialog
    var productId = ""
    private var flList: java.util.ArrayList<FLBean.Data> = java.util.ArrayList()
    private var asmList: java.util.ArrayList<ACPFIlterBean.Data.ASM> = java.util.ArrayList()
    private var rsmList: java.util.ArrayList<ACPFIlterBean.Data.RSM> = java.util.ArrayList()

    var checkUserId: String = ""
    var selectedDate = ""
    var cropId = ""
    var cropName = ""
    private var type = ""
    private var ID = ""
    var fromDate = ""
    var toDate = ""
    var whichTabSelected = ""
    var adapter: SelectDdAdapter? = null
    var status = ""
    var userType: String = ""
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_my_lead_filter)
        preference = Preference(this)
        userType = preference.getString("user_type")
        if (userType.equals("BDO", false)) {
            flDd.visibility = View.VISIBLE
        } else {
            flDd.visibility = View.GONE
        }
        //get crop list
        updateCropList()
        //apply click on view
        applyCLickOnView()

        intent?.let {
            //get dd list
            if (it.hasExtra(Constants.DDLIST)) {
                ddDetailList =
                    it.getSerializableExtra(Constants.DDLIST) as ArrayList<TeamSummaryDdDetail>
                setDemoFilterAdapter(ddDetailList)
            }
            if (it.hasExtra("whichtabselected")) {
                whichTabSelected = it.getStringExtra("whichtabselected") ?: ""
            }
        }

        llASM.visibility = View.GONE
        llRSM.visibility = View.GONE
        llFL.visibility = View.GONE

        iCross.setOnClickListener {
            this@MyLeadFilterActivity.finish()
        }
    }

    private fun setDemoFilterAdapter(ddDetailList: ArrayList<TeamSummaryDdDetail>) {
        rvDdName.layoutManager =
            LinearLayoutManager(this@MyLeadFilterActivity)
        adapter = SelectDdAdapter(this@MyLeadFilterActivity, ddDetailList)
        rvDdName.adapter = adapter
    }

    /**
     * Apply click on view
     */
    private fun applyCLickOnView() {
        btnClear.setOnClickListener(this)
        btnApply.setOnClickListener(this)
        llFrom.setOnClickListener(this)
        llTo.setOnClickListener(this)
        llOriginalDd.setOnClickListener(this)
        llDdWithRecycler.setOnClickListener(this)
        llCrop.setOnClickListener(this)
        llProduct.setOnClickListener(this)
    }


    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.llOriginalDd -> {
                llOriginalDd.visibility = View.GONE
                llDdWithRecycler.visibility = View.VISIBLE
            }
            R.id.llDdWithRecycler -> {
                llOriginalDd.visibility = View.VISIBLE
                llDdWithRecycler.visibility = View.GONE
            }
            R.id.btnClear -> {
                clearForm()
            }
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
                            //get product list
                            getProductList(cropId)
                        }
                    }

                )
                cropSpinnerDialog.showSpinerDialog()
            }
            R.id.llProduct -> {
                if (cropProductList.size > 0) {
                    cropProductSpinnerDialog = CropProductSpinnerDialog(
                        this,
                        cropProductList,
                        "Select or Search Product",
                        object : OnGetId {
                            override fun onClick(item: String, position: Int, productID: String) {
                                txtProduct.text = item
                                productId = productID
                            }

                        })
                    cropProductSpinnerDialog.showSpinerDialog()
                }
            }
            R.id.llFrom -> {
                showDate(txtFrom)
            }
            R.id.llTo -> {
                showDate(txtTo)
            }
            R.id.btnApply -> {
                fromDate = txtFrom.text.toString()
                toDate = txtTo.text.toString()
                list.clear()
                for (item in 0..ddDetailList.size - 1) {
                    if (ddDetailList.get(item).isChecked) {
                        list.add(ddDetailList.get(item).dd_id)
                    }
                }
                for (item in 0..list.size - 1) {
                    if (item == 0) {
                        checkUserId = ddDetailList.get(item).dd_id
                    } else {
                        checkUserId = checkUserId + "," + ddDetailList.get(item).dd_id
                    }

                }
                if (whichTabSelected.equals("open")) {
                    status = "1"
                } else {
                    status = "2"
                }

                val intent = Intent()
                intent.putExtra(Constants.CHECKED_USER_ID, checkUserId)
                intent.putExtra("cropid", cropId)
                intent.putExtra("productid", productId)
                intent.putExtra("fromdate", fromDate)
                intent.putExtra("todate", toDate)
                intent.putExtra("status", status)
                setResult(Activity.RESULT_OK, intent)
                this@MyLeadFilterActivity.finish()

            }

        }
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

    /**
     * function to clear form
     */
    fun clearForm() {
        for (item in 0..ddDetailList.size - 1) {
            ddDetailList.get(item).isChecked = false
        }
        if (adapter != null) {
            adapter?.notifyDataSetChanged()
        }
        txtDdName.text = ""
        txtFrom.text = ""
        txtTo.text = ""
        txtCrop.text = ""
        txtProduct.text = ""
    }

    //get crop productListData
    private fun getProductList(cropId: String) {
        showProgress()
        var model: String = preference.getString("token")
        val query = HashMap<String, Any>()
        query.put("apiToken", model)
        query.put("crop_id", cropId)
        var gson = Gson()
        var j = gson.toJson(query)

        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .getpRoductResponse(query).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<CropProductList>() {
                override fun onCompleted() {
                    hideProgress()

                }

                override fun onError(e: Throwable) {
                    //listener.onFailure(e.localizedMessage ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection.")
                    hideProgress()

                    makeToast(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onNext(listBean: CropProductList) {
                    hideProgress()
                    if (listBean.status_code == 200) {
                        cropProductList.clear()
                        cropProductList.addAll(listBean.data)

                    } else {
                        //listener.onFailure(listBean.message)

                    }
                }
            })

    }


    private fun getFilterList(token: String) {
        showProgress()
        val query = HashMap<String, Any>()
        query["apiToken"] = token
        query.put("type", type)
        query.put("id", ID)
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).departmentlist(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ACPFIlterBean>() {
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

                override fun onNext(villageBean: ACPFIlterBean) {
                    hideProgress()
                    if (villageBean.statusCode == 200) {
                        rsmList.addAll(villageBean.data.rSMList)
                        asmList.addAll(villageBean.data.aSMList)
                        //flList.addAll(villageBean.data.bDOList)

                    }


                }
            }
            )
    }
    //end

}