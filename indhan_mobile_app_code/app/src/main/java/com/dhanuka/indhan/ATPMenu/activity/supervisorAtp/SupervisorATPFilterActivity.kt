package com.dhanuka.indhan.ATPMenu.activity.supervisorAtp

import android.app.Activity
import android.app.DatePickerDialog
import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.TextView
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.NewTicketMVP.*
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.CropBean
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.ProductBean
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.DataBase.TableDataModel.ProductCropResponse
import com.dhanuka.indhan.DemoRequestActivity.model.ProductListResponseNew
import com.dhanuka.indhan.DemoRequestEvaluated.adapters.SelectDdAdapter
import com.dhanuka.indhan.PlanATP.View.DDListActivity
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.acpfarmers.ACPFIlterBean
import com.dhanuka.indhan.teamsummary.models.TeamSummaryDdDetail
import com.dhanuka.indhan.teamsummary.models.TeamSummaryResponseNew
import com.dhanuka.indhan.util.OnGetId
import com.dhanuka.indhan.util.OnSpinerItemClick
import com.google.gson.Gson
import kotlinx.android.synthetic.main.activity_supervisor_atpfilter.*
import org.greenrobot.eventbus.EventBus
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*

class SupervisorATPFilterActivity : BaseActivity() {


    lateinit var adapter: SelectDdAdapter
    var list: ArrayList<String> = ArrayList()
    private lateinit var preference: Preference
    var selectedDate = ""


    private lateinit var asmNameDialog: ASMNameDialog
    private lateinit var rsmNameDialog: RSMNameDialog
    private lateinit var flSpinnerDialog: FLNameDialog
    private lateinit var ddSpinnerDialog: DDFilterNameDialog


    private var flList: ArrayList<ACPFIlterBean.Data.BDO> = java.util.ArrayList()
    private var asmList: ArrayList<ACPFIlterBean.Data.ASM> = java.util.ArrayList()
    private var rsmList: ArrayList<ACPFIlterBean.Data.RSM> = java.util.ArrayList()
    private var ddList: ArrayList<ACPFIlterBean.Data.DD> = java.util.ArrayList()


    private lateinit var cropSpinnerDialog: CropSpinnerDialogNew
    private lateinit var productSpinnerDialog: ProductSpinnerDialog
    private val crop1RecordResponseModel = ArrayList<CropBean>()
    private var prodCropResponseModel: ArrayList<ProductCropResponse.ProductCropList> = ArrayList()
    private var productList: ArrayList<ProductBean> = ArrayList()
    lateinit var ddDetailList: ArrayList<TeamSummaryDdDetail>

    var cropId = ""
    var productId = ""
    var productName = ""
    var cropName = ""
    var fromDate = ""
    var toDate = ""
    var DDID = ""
    private var fl = ""
    private var asmId = ""
    private var rsmId = ""
    private var type = ""
    private var ID = ""
    private var isFL = false
    private var isClicked = false
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_supervisor_atpfilter)


        if (intent.hasExtra("from")) {
            isFL = true
        }

        preference = Preference(this)
        if (!preference.getString("user_type").equals("BDO", false)) {
            when {
                preference.getString("user_type").equals("SALES_REGIONAL") -> {
                    ID = preference.getString("user_id")
                    type = "sales_regional"
                    llASM.visibility = View.VISIBLE
                    llFL.visibility = View.VISIBLE
                    llDD.visibility = View.VISIBLE
                    frameDD.visibility = View.GONE
                    getFilterList(preference.getString("token"))
                }
                preference.getString("user_type").equals("SALES_AREA") -> {
                    ID = preference.getString("user_id")
                    type = "sales_area"
                    llFL.visibility = View.VISIBLE
                    llDD.visibility = View.VISIBLE
                    frameDD.visibility = View.GONE
                    getFilterList(preference.getString("token"))
                }
                preference.getString("user_type").equals("dhanuka_doctor", false) -> {
                    llRSM.visibility = View.GONE
                    llASM.visibility = View.GONE
                    llFL.visibility = View.GONE
                    llDD.visibility = View.GONE
                    frameDD.visibility = View.GONE
                }
                else -> {
                    llRSM.visibility = View.VISIBLE
                    llASM.visibility = View.VISIBLE
                    llFL.visibility = View.VISIBLE
                    llDD.visibility = View.VISIBLE
                    frameDD.visibility = View.GONE
                    getFilterList(preference.getString("token"))
                }
            }
        }

        flSpinnerDialog = FLNameDialog(this, flList, "Select FL", object : OnGetId {
            override fun onClick(item: String, position: Int, sId: String) {
                txtFL.text = flList[position].name
                fl = flList[position].id
                ID = sId
                type = "bdo"

                ddList.clear()
                txtDdName.text = ""
                DDID = ""
                getFilterList(preference.getString("token"))
            }

        })

        asmNameDialog = ASMNameDialog(this, asmList, "Select ASM", object : OnGetId {
            override fun onClick(item: String, position: Int, sId: String) {
                txtASM.text = item
                asmId = sId
                ID = sId
                type = "sales_area"
                flList.clear()
                ddList.clear()
                txtFL.text = ""
                txtDdName.text = "Select DD"
                fl = ""
                DDID = ""
                getFilterList(preference.getString("token"))

            }

        })

        rsmNameDialog = RSMNameDialog(this, rsmList, "Select RSM", object : OnGetId {
            override fun onClick(item: String, position: Int, sId: String) {
                txtRSM.text = item
                rsmId = sId
                ID = sId
                type = "sales_regional"
                asmList.clear()
                flList.clear()
                ddList.clear()
                txtASM.text = ""
                txtFL.text = ""
                txtDdName.text = "Select DD"
                asmId = ""
                fl = ""
                DDID = ""
                getFilterList(preference.getString("token"))


            }

        })

        ddSpinnerDialog = DDFilterNameDialog(this, ddList, "Select DD", object : OnGetId {
            override fun onClick(item: String, position: Int, sId: String) {
                txtDD.text = item
                DDID = sId
                ID = sId
                //type = "sales_regional"
                //getFilterList(preference.getString("token"))


            }

        })


        //get crop list
        updateCropList()
        //get product list
        getProductList()
        //apply click on view
        applyClickOnView()

        //callTeamSummaryApi()

        llOriginalDd.setOnClickListener {
            var intent = Intent(this, DDListActivity::class.java)
            startActivityForResult(intent, 100)
        }


    }


    /**
     * apply click on click on view
     */

    fun applyClickOnView() {
        btnClear.setOnClickListener(this)
        btnApply.setOnClickListener(this)
        iCross.setOnClickListener(this)
        llOriginalDd.setOnClickListener(this)
        llDdWithRecycler.setOnClickListener(this)
        llFrom.setOnClickListener(this)
        llTo.setOnClickListener(this)
        llProduct.setOnClickListener(this)
        llCrop.setOnClickListener(this)
        llRSM.setOnClickListener(this)
        llASM.setOnClickListener(this)
        llFL.setOnClickListener(this)
        llDD.setOnClickListener(this)
    }

    /**
     * Get crop list
     */
    private fun updateCropList() {
        prodCropResponseModel = DatabaseManager.getProductCropDetails(this)
        val prpdCropListNew = DatabaseManager.getProductCropDetailProductNew(this)
        prpdCropListNew?.forEach {
            val bean = CropBean()
            bean.setcID(it.cropId.toInt())
            bean.item = it.crop
            if (!checkValue(bean.getcID()!!))
                crop1RecordResponseModel.add(bean)
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
        val model: String = preference.getString("token")
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).getProductListNew(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ProductListResponseNew>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    //listener.onFailure(e.localizedMessage ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection.")
                }

                override fun onNext(listBean: ProductListResponseNew) {
                    listBean.let {
                        if (it != null) {
                            if (it.status_code == 409) {
                                EventBus.getDefault().post("logout")
                            }
                        }
                    }
                }
            })

    }


    private fun callTeamSummaryApi() {
        showProgress()
        var model: String = preference.getString("token")
        val query = java.util.HashMap<String, Any>()
        query.put("apiToken", model)


        var gson = Gson()
        var j = gson.toJson(query)

        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java)
            .getpTeamSummaryResponse(query).subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<TeamSummaryResponseNew>() {
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

                override fun onNext(listBean: TeamSummaryResponseNew) {
                    hideProgress()
                    if (listBean.status_code == 200) {
                        ddDetailList.addAll(listBean.data.ddDetail)
                        rvDdName.layoutManager =
                            LinearLayoutManager(this@SupervisorATPFilterActivity)
                        adapter = SelectDdAdapter(this@SupervisorATPFilterActivity, ddDetailList)
                        rvDdName.adapter = adapter
                    } else {

                    }
                }
            })

    }

    //end


    /**
     * function for clicking on view
     */
    override fun onClick(v: View?) {
        when (v?.id) {

            R.id.btnClear -> {
                //clear form
                clearForm()
            }

            R.id.btnApply -> {
                if (!isClicked) {
                    isClicked = true

                    if (!isFL) {
                        fromDate = txtFrom.text.toString()
                        toDate = txtTo.text.toString()
                        cropName = txtCrop.text.toString()
                        productName = txtProduct.text.toString()
                        list.clear()


                        val intent =
                            Intent(this@SupervisorATPFilterActivity, callingActivity!!::class.java)
                        intent.putExtra(Constants.PRODUCT_NAME, DDID)
                        intent.putExtra(Constants.CROP_NAME, cropName)
                        intent.putExtra(Constants.PRODUCT_ID, productId)
                        intent.putExtra(Constants.CROP_ID, cropId)
                        intent.putExtra(Constants.FROM_DATE, fromDate)
                        intent.putExtra(Constants.TO_DATE, toDate)
                        intent.putExtra(Constants.FROM, "filter")
                        intent.putExtra("FLID", fl)
                        intent.putExtra("RSMID", rsmId)
                        intent.putExtra("ASMID", asmId)

                        /*
                    * set data to shared prefrece
                    * */
                        preference.putString(Constants.PRODUCT_NAME, DDID)
                        preference.putString(Constants.CROP_NAME, cropName)
                        preference.putString(Constants.PRODUCT_ID, productId)
                        preference.putString(Constants.CROP_ID, cropId)
                        preference.putString(Constants.FROM_DATE, fromDate)
                        preference.putString(Constants.TO_DATE, toDate)
                        preference.putString("FLID", fl)
                        preference.putString("RSMID", rsmId)
                        preference.putString("ASMID", asmId)
                        setResult(120, intent)
                        makeToast("Filter is applied for all tabs")
                        hideProgress()
                        finish()
                    } else {
                        fromDate = txtFrom.text.toString()
                        toDate = txtTo.text.toString()
                        cropName = txtCrop.text.toString()
                        productName = txtProduct.text.toString()
                        list.clear()


                        val intent =
                            Intent(this@SupervisorATPFilterActivity, callingActivity!!::class.java)
                        intent.putExtra(Constants.PRODUCT_NAME, DDID)
                        intent.putExtra(Constants.CROP_NAME, cropName)
                        intent.putExtra(Constants.PRODUCT_ID, productId)
                        intent.putExtra(Constants.CROP_ID, cropId)
                        intent.putExtra(Constants.FROM_DATE, fromDate)
                        intent.putExtra(Constants.TO_DATE, toDate)
                        intent.putExtra(Constants.FROM, "filter")
                        intent.putExtra("FLID", fl)
                        intent.putExtra("RSMID", rsmId)
                        intent.putExtra("ASMID", asmId)

                        /*
                    * set data to shared prefrece
                    * */
                        preference.putString(Constants.PRODUCT_NAME, DDID)
                        preference.putString(Constants.CROP_NAME, cropName)
                        preference.putString(Constants.PRODUCT_ID, productId)
                        preference.putString(Constants.CROP_ID, cropId)
                        preference.putString(Constants.FROM_DATE, fromDate)
                        preference.putString(Constants.TO_DATE, toDate)
                        preference.putString("FLID", fl)
                        preference.putString("RSMID", rsmId)
                        preference.putString("ASMID", asmId)
                        setResult(Activity.RESULT_OK, intent)
                        finish()
                    }
                }

            }

            R.id.iCross -> {
                finish()
            }
            R.id.llFrom -> {
                showDate(txtFrom)
            }
            R.id.llTo -> {
                showDate(txtTo)
            }
            R.id.llCrop -> {
                cropSpinnerDialog = CropSpinnerDialogNew(
                    this,
                    crop1RecordResponseModel,
                    "Select or Search Crop",
                    object : OnGetId {
                        override fun onClick(item: String, position: Int, cropID: String) {
                            txtCrop.text = item
                            updateProductBeanList(cropID.toInt(), position)
                            cropId = cropID
                            cropName = item
                            txtProduct.text = "select product"
                            productId = ""
                            productName = ""
                        }
                    }

                )
                cropSpinnerDialog.showSpinerDialog()
            }
            R.id.llProduct -> {
                if (productList.size > 0) {

                    productSpinnerDialog =
                        ProductSpinnerDialog(this, productList, "Select or Search Product")
                    productSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
                        override fun onClick(item: String, position: Int) {
                            txtProduct.text = item
                            productId = productList[position].getpID().toString()
                            productName = item
                        }

                    })
                    //productSpinnerDialog = ProductSpinnerDialog(this, productList, "Select or Search Product", object : OnGetId {


                    /*
                    *  txtProduct.text = item
                            productId = productID
                            productName = item
                    * */
                    productSpinnerDialog.showSpinerDialog()
                }

            }
            R.id.llOriginalDd -> {
                llOriginalDd.visibility = View.GONE
                llDdWithRecycler.visibility = View.VISIBLE
            }
            R.id.llDdWithRecycler -> {
                llOriginalDd.visibility = View.VISIBLE
                llDdWithRecycler.visibility = View.GONE
            }
            R.id.llFL -> {
                if (!flSpinnerDialog.isEnable)
                    flSpinnerDialog.showSpinerDialog()

            }
            R.id.llRSM -> {
                if (!rsmNameDialog.isEnable)
                    rsmNameDialog.showSpinerDialog()
            }
            R.id.llASM -> {
                if (!asmNameDialog.isEnable)
                    asmNameDialog.showSpinerDialog()
            }
            R.id.llDD -> {
                if (!ddSpinnerDialog.isEnable)
                    ddSpinnerDialog.showSpinerDialog()
            }

        }
    }


    /*
show date
*/
    private fun showDate(txtFromDate: TextView): Unit {
        val c = Calendar.getInstance( Locale.ENGLISH)
        val year = c.get(Calendar.YEAR)
        val month = c.get(Calendar.MONTH)
        val day = c.get(Calendar.DAY_OF_MONTH)
        var m = ""
        var d = ""
        val mDatePicker = DatePickerDialog(
            activity,
            DatePickerDialog.OnDateSetListener { view, year, monthOfYear, dayOfMonth ->

                // Display Selected date in textbox
                //lblDate.setText("" + dayOfMonth + " " + MONTHS[monthOfYear] + ", " + year)
                if ((monthOfYear + 1) < 10) {
                    m = "0" + (monthOfYear + 1)
                } else {
                    m = "" + (monthOfYear + 1)
                }
                if (dayOfMonth < 10) {
                    d = "0" + dayOfMonth
                } else {
                    d = "" + dayOfMonth
                }
                selectedDate = "" + year + "-" + m + "-" + d
                // selectedDate = "" + year + "-" + (monthOfYear + 1) + "-" + dayOfMonth
                txtFromDate.text = selectedDate

            },
            year,
            month,
            day
        )
        //  mDatePicker.getDatePicker().setMaxDate(System.currentTimeMillis());
        //mDatePicker.getDatePicker().setMaxDate(System.currentTimeMillis());

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
        txtFrom.text = ""
        txtTo.text = ""
        txtCrop.text = ""
        txtProduct.text = ""
        txtASM.text = ""
        txtRSM.text = ""
        txtFL.text = ""
        txtDdName.text = ""
        txtDD.text = "Select DD"
        cropId = ""
        productId = ""
        productName = ""
        cropName = ""
        fromDate = ""
        toDate = ""
        DDID = ""
        var fl = ""
        var asmId = ""
        var rsmId = ""
        var type = ""
        var ID = ""

    }


    /**
     * call all list of supervisor, FL,DD
     * */
    private fun getFilterList(token: String) {
        showProgress()
        val query = HashMap<String, Any>()
        query["type"] = type
        query["apiToken"] = token
        query["id"] = ID


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
                        flList.addAll(villageBean.data.bDOList)
                        ddList.addAll(villageBean.data.dDList)

                    }


                }
            }
            )
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == 100 && resultCode == 100) {
            DDID = data?.getStringExtra("id") ?: ""
            txtDdName.text = data?.getStringExtra("ddName") ?: ""
        }
    }

    private fun updateProductBeanList(id: Int, position: Int) {
        val temp = ArrayList<ProductBean>()
        prodCropResponseModel.forEach {
            if (it.cropId.equals(id.toString())) {
                var product = ProductBean()
                product.setpID(it.productId.toInt())
                product.item = it.product
                temp.add(product)
            }
        }
        productList.clear()
        productList.addAll(temp)
    }
}
