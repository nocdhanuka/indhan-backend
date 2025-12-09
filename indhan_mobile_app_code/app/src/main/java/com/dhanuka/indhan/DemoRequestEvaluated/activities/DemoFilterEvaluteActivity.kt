package com.dhanuka.indhan.DemoRequestEvaluated.activities

import android.app.Activity
import android.app.AlertDialog
import android.app.DatePickerDialog
import android.content.Intent
import android.os.Bundle
import android.text.Editable
import android.text.TextWatcher
import android.view.View
import android.widget.*
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.NewTicketMVP.*
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.CropBean
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.ProductBean
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.DataBase.TableDataModel.ProductCropResponse
import com.dhanuka.indhan.DemoRequestActivity.model.ProductListResponseNew
import com.dhanuka.indhan.DemoRequestActivity.model.ProductlistNew
import com.dhanuka.indhan.DemoRequestEvaluated.adapters.SelectDdNewAdapter
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.acpfarmers.ACPFIlterBean
import com.dhanuka.indhan.util.OnGetId
import com.dhanuka.indhan.util.OnSpinerItemClick
import kotlinx.android.synthetic.main.activity_demo_filter_evalute.*
import kotlinx.android.synthetic.main.activity_demo_filter_evalute.btnApply
import kotlinx.android.synthetic.main.activity_demo_filter_evalute.btnClear
import kotlinx.android.synthetic.main.activity_demo_filter_evalute.iCross
import kotlinx.android.synthetic.main.activity_demo_filter_evalute.llASM
import kotlinx.android.synthetic.main.activity_demo_filter_evalute.llCrop
import kotlinx.android.synthetic.main.activity_demo_filter_evalute.llDD
import kotlinx.android.synthetic.main.activity_demo_filter_evalute.llFL
import kotlinx.android.synthetic.main.activity_demo_filter_evalute.llFrom
import kotlinx.android.synthetic.main.activity_demo_filter_evalute.llProduct
import kotlinx.android.synthetic.main.activity_demo_filter_evalute.llRSM
import kotlinx.android.synthetic.main.activity_demo_filter_evalute.llTo
import kotlinx.android.synthetic.main.activity_demo_filter_evalute.txtASM
import kotlinx.android.synthetic.main.activity_demo_filter_evalute.txtCrop
import kotlinx.android.synthetic.main.activity_demo_filter_evalute.txtDdName
import kotlinx.android.synthetic.main.activity_demo_filter_evalute.txtFL
import kotlinx.android.synthetic.main.activity_demo_filter_evalute.txtFrom
import kotlinx.android.synthetic.main.activity_demo_filter_evalute.txtProduct
import kotlinx.android.synthetic.main.activity_demo_filter_evalute.txtRSM
import kotlinx.android.synthetic.main.activity_demo_filter_evalute.txtTo
import kotlinx.android.synthetic.main.activity_supervisor_atpfilter.*
import kotlinx.android.synthetic.main.activity_testimonial.*
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*
import kotlin.collections.ArrayList

class DemoFilterEvaluteActivity : BaseActivity() {
    var ddDetailList: ArrayList<ACPFIlterBean.Data.DD> = ArrayList()
    var ddDetailListMain: ArrayList<ACPFIlterBean.Data.DD> = ArrayList()

    private lateinit var asmNameDialog: ASMNameDialog
    private lateinit var rsmNameDialog: RSMNameDialog
    private lateinit var flSpinnerDialog: FLNameDialog

    private var flList: ArrayList<ACPFIlterBean.Data.BDO> = java.util.ArrayList()
    private var asmList: ArrayList<ACPFIlterBean.Data.ASM> = java.util.ArrayList()
    private var rsmList: ArrayList<ACPFIlterBean.Data.RSM> = java.util.ArrayList()

    private var type = ""
    private var ID = ""
    private var fl = ""
    private var asmId = ""
    private var rsmId = ""
    var checkUserId: String = ""
    var list: ArrayList<String> = ArrayList()

    var selectedDate = ""
    var whichTabSelected = ""
    var adapter: SelectDdNewAdapter? = null
    private lateinit var preference: Preference
    private lateinit var cropSpinnerDialog: CropSpinnerDialogNew
    private lateinit var productSpinnerDialog: ProductSpinnerDialogNew
    private val crop1RecordResponseModel = ArrayList<CropBean>()
    private var prodCropResponseModel: ArrayList<ProductCropResponse.ProductCropList> = ArrayList()
    private var productList: ArrayList<ProductlistNew> = ArrayList()
    private var productList1: ArrayList<ProductBean> = ArrayList()
    var cropId = ""
    var productId = ""
    var productName = ""
    var cropName = ""
    var fromDate = ""
    var toDate = ""
    internal var closeTitle = "Close"
    internal var isEnable = false
    internal lateinit var alertDialog: AlertDialog
    internal var style: Int = 0
    private lateinit var productSpinner: ProductSpinnerDialog

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_demo_filter_evalute)
        preference = Preference(this)
        getIntentData()
        preference = Preference(this)
        if (!preference.getString("user_type").equals("BDO", false)) {
            when {
                preference.getString("user_type").equals("SALES_REGIONAL") -> {
                    ID = preference.getString("user_id")
                    type = "sales_regional"
                    llASM.visibility = View.VISIBLE
                    llFL.visibility = View.VISIBLE
                    llDD.visibility = View.VISIBLE
                    getFilterList(preference.getString("token"))
                }
                preference.getString("user_type").equals("SALES_AREA") -> {
                    ID = preference.getString("user_id")
                    type = "sales_area"
                    llFL.visibility = View.VISIBLE
                    llDD.visibility = View.VISIBLE
                    getFilterList(preference.getString("token"))
                }
                preference.getString("user_type").equals("dhanuka_doctor", false) -> {
                    llRSM.visibility = View.GONE
                    llASM.visibility = View.GONE
                    llFL.visibility = View.GONE
                    llDD.visibility = View.GONE
                }
                else -> {
                    llRSM.visibility = View.VISIBLE
                    llASM.visibility = View.VISIBLE
                    llFL.visibility = View.VISIBLE
                    llDD.visibility = View.VISIBLE
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
                txtDdName.text = ""
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
                txtFL.text = ""
                txtDdName.text = "Select DD"
                fl = ""
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
                txtASM.text = ""
                txtFL.text = ""
                txtDdName.text = "Select DD"
                asmId = ""
                fl = ""
                getFilterList(preference.getString("token"))


            }

        })

        productSpinner = ProductSpinnerDialog(this, productList1, "Select Product")

        productSpinner.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                txtProduct.text = item
                productId = productList1.get(position).getpID().toString()
                productName = item

            }
        })

        //get crop list
        updateCropList()
        //get product list
        getProductList()
        //apply click on view
        applyClickOnView()

    }

    private fun getIntentData() {
        intent?.let {
            if (it.hasExtra("whichtabselected")) {
                whichTabSelected = it.getStringExtra("whichtabselected") ?: ""
            }
            if (it.hasExtra(Constants.FROM_DATE)) {
                fromDate = it.getStringExtra(Constants.FROM_DATE) ?: ""
            }
            if (it.hasExtra(Constants.TO_DATE)) {
                toDate = it.getStringExtra(Constants.TO_DATE) ?: ""
            }
            if (it.hasExtra(Constants.PRODUCT_ID)) {
                productId = it.getStringExtra(Constants.PRODUCT_ID) ?: ""
            }
            if (it.hasExtra(Constants.CROP_ID)) {
                cropId = it.getStringExtra(Constants.CROP_ID) ?: ""
            }
            if (it.hasExtra(Constants.PRODUCT_NAME)) {
                productName = it.getStringExtra(Constants.PRODUCT_NAME) ?: ""
            }
            if (it.hasExtra(Constants.CROP_NAME)) {
                cropName = it.getStringExtra(Constants.CROP_NAME) ?: ""
            }

            updateData()
        }

    }

    private fun updateData() {
        txtFrom.text = fromDate
        txtTo.text = toDate
        txtCrop.text = cropName
        txtProduct.text = productName
    }

    /**
     * apply click on click on view
     */

    fun applyClickOnView() {
        btnClear.setOnClickListener(this)
        btnApply.setOnClickListener(this)
        iCross.setOnClickListener(this)

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
        if (prpdCropListNew != null) {
            prodCropResponseModel.forEach {
                val bean = CropBean()
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
                    if (listBean.status_code == 200) {
                        productList.clear()
                        productList.addAll(listBean.data.productlist)

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
                clearForm()
            }

            R.id.btnApply -> {
                fromDate = txtFrom.text.toString()
                toDate = txtTo.text.toString()
                cropName = txtCrop.text.toString()
                productName = txtProduct.text.toString()

                list.clear()

                for (item in 0..ddDetailList.size - 1) {
                    if (ddDetailList.get(item).isChecked) {
                        list.add(ddDetailList.get(item).ddId)
                    }
                }

                for (item in 0..list.size - 1) {
                    if (item == 0) {
                        checkUserId = ddDetailList.get(item).ddId
                    } else {
                        checkUserId = checkUserId + "," + ddDetailList.get(item).ddId
                    }
                }

                val intent = Intent()
                intent.putExtra(Constants.PRODUCT_NAME, productName)
                intent.putExtra(Constants.CROP_NAME, cropName)
                intent.putExtra(Constants.CHECKED_USER_ID, checkUserId)
                intent.putExtra(Constants.DDARRAY, list)
                intent.putExtra(Constants.PRODUCT_ID, productId)
                intent.putExtra(Constants.CROP_ID, cropId)
                intent.putExtra(Constants.FROM_DATE, fromDate)
                intent.putExtra(Constants.TO_DATE, toDate)
                intent.putExtra(Constants.FROM, "filter")
                intent.putExtra(Constants.WHICHTAB_SELECTED, whichTabSelected)
                setResult(Activity.RESULT_OK, intent)
                finish()

            }

            R.id.iCross -> {
                this@DemoFilterEvaluteActivity.finish()
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
                            cropId = cropID
                            cropName = item

                            txtProduct.text = ""
                            productId = ""
                            productName = ""
                            updateProductList(cropID.toInt())
                        }
                    }

                )
                cropSpinnerDialog.showSpinerDialog()
            }

            R.id.llProduct -> {
                if (productList.size > 0) {
                    /* productSpinnerDialog = ProductSpinnerDialogNew(this, productList, "Select or Search Product", object : OnGetId {
                         override fun onClick(item: String, position: Int, productID: String) {
                             txtProduct.text = item
                             productId = productID
                             productName = item
                         }

                     })
                     productSpinnerDialog.showSpinerDialog()*/
                    productSpinner.showSpinerDialog()
                }

            }

            R.id.llDD -> {
                selectDDDialog()
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
        for (item in 0 until ddDetailList.size) {
            ddDetailList.get(item).isChecked = false
        }

        txtFrom.text = ""
        txtTo.text = ""
        txtCrop.text = ""
        txtProduct.text = ""

        txtASM.text = "Select ASM"
        txtRSM.text = "Select RSM"
        txtFL.text = "Select FL"
        txtDdName.text = "Select DD"
        asmId = ""
        rsmId = ""
        fl = ""
        checkUserId = ""
        ID = ""
        productId = ""
        cropId = ""
        adapter?.notifyDataSetChanged()
    }

    private fun selectDDDialog() {
        isEnable = true
        val adb = AlertDialog.Builder(this)
        val v = layoutInflater.inflate(R.layout.dialog_ddlayout, null)
        val rippleViewClose = v.findViewById<View>(R.id.close) as TextView
        val title = v.findViewById<View>(R.id.spinerTitle) as TextView
        rippleViewClose.text = closeTitle
        title.text = "Select or Search DD"
        val recyleViewDd = v.findViewById<View>(R.id.rvDdName) as RecyclerView
        val searchBox = v.findViewById<View>(R.id.searchBox) as EditText

        recyleViewDd.layoutManager =
            LinearLayoutManager(this@DemoFilterEvaluteActivity)
        adapter = SelectDdNewAdapter(this@DemoFilterEvaluteActivity, ddDetailList)
        recyleViewDd.adapter = adapter

        adb.setView(v)
        alertDialog = adb.create()
        alertDialog.window!!.attributes.windowAnimations =
            style //R.style.DialogAnimations_SmileWindow;
        alertDialog.setCancelable(false)

        searchBox.addTextChangedListener(object : TextWatcher {
            override fun beforeTextChanged(charSequence: CharSequence, i: Int, i1: Int, i2: Int) {

            }

            override fun onTextChanged(charSequence: CharSequence, i: Int, i1: Int, i2: Int) {

            }

            override fun afterTextChanged(editable: Editable) {
                val str: String = editable.toString().toLowerCase(Locale.getDefault())
                ddDetailList.clear()
                if (str == "") {
                    ddDetailList.addAll(ddDetailListMain)
                } else {
                    for (list in ddDetailListMain) {
                        if (list.name.toLowerCase(Locale.getDefault()).contains(str)) {
                            ddDetailList.add(list)
                        }
                    }
                }
                adapter?.notifyDataSetChanged()
            }
        })

        rippleViewClose.setOnClickListener {
            isEnable = false
            alertDialog.dismiss()
        }

        alertDialog.show()
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
                        ddDetailList.clear()
                        ddDetailListMain.clear()
                        ddDetailList.addAll(villageBean.data.dDList)
                        ddDetailListMain.addAll(villageBean.data.dDList)
                    }
                }
            }
            )
    }

    private fun updateProductList(crop_id: Int) {
        productList1.clear()
        prodCropResponseModel.forEach {
            if (it.cropId.equals(crop_id.toString())) {
                val crop = ProductBean()
                crop.setpID(it.productId.toInt())
                crop.item = it.product
                productList1.add(crop)
            }
        }
        productSpinner.updatelist(productList1)

    }
}
