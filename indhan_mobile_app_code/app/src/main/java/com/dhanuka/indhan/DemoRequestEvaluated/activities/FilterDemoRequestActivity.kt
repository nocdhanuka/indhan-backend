package com.dhanuka.indhan.DemoRequestEvaluated.activities

import android.content.Intent
import android.os.Bundle
import android.text.TextUtils
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.NewTicketMVP.CropSpinnerDialog
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.CropBean
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.ProductBean
import com.dhanuka.indhan.Activity.NewTicketMVP.ProductSpinnerDialog
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.DataBase.TableDataModel.ProductCropResponse
import com.dhanuka.indhan.DemoRequestEvaluated.models.GoDownData
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.OnSpinerItemClick
import com.dhanuka.indhan.util.SelectDateFragment
import kotlinx.android.synthetic.main.demo_request_filter_activity.*
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.GlobalScope
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import java.util.*

class FilterDemoRequestActivity : BaseActivity(), SelectDateFragment.myDialogListener {
    private var appDataBaseClient: AppDatabase? = null
    private lateinit var preference: Preference
    private var prodCropResponseModel: ArrayList<ProductCropResponse.ProductCropList> = ArrayList()
    private var goDownList: ArrayList<GoDownData> = ArrayList()
    private var goDownListString: ArrayList<String> = ArrayList()
    private var productList: ArrayList<ProductBean> = ArrayList()
    private var cropList: ArrayList<CropBean> = ArrayList()
    private var productSpinner: ProductSpinnerDialog? = null
    private var cropSpinner: CropSpinnerDialog? = null
    private var goDownSpinner: NumberSpinner? = null
    private var product_id = 0
    private var product = ""
    private var crop_id = 0
    private var crop = ""
    private var fromDateNeeded: String = ""
    private var toDateNeeded: String = ""
    private var goDown_id = 0
    private var goDownName = ""
    private var fromDateRequested: String = ""
    private var toDateRequested: String = ""

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.demo_request_filter_activity)
        preference = Preference(this)
        appDataBaseClient = DatabaseClient.getInstance(this).appDatabase
        setFilterData()
        updateCropList()
        setListeners()
        setDataForSpinner()
    }

    private fun setFilterData() {
        intent.getBundleExtra("bundle")?.let {
            crop = it.getString("crop_name") ?: ""
            product = it.getString("product_name") ?: ""
            fromDateNeeded = it.getString("needed_start") ?: ""
            toDateNeeded = it.getString("needed_end") ?: ""
            goDownName = it.getString("godown_name") ?: ""
            fromDateRequested = it.getString("requested_from") ?: ""
            toDateRequested = it.getString("requested_to") ?: ""
            crop_id = it.getInt("crop_id", 0)
            product_id = it.getInt("product_id", 0)
            goDown_id = it.getInt("godown_id", 0)

            if (!TextUtils.isEmpty(crop)) {
                cropName.text = crop
            }
            if (!TextUtils.isEmpty(product)) {
                productName.text = product
            }
            if (!TextUtils.isEmpty(fromDateNeeded)) {
                fromDate.text = fromDateNeeded
            }
            if (!TextUtils.isEmpty(toDateNeeded)) {
                toDate.text = toDateNeeded
            }
            if (!TextUtils.isEmpty(goDownName)) {
                goDown.text = goDownName
            }
            if (!TextUtils.isEmpty(fromDateRequested)) {
                fromDateRequestedBy.text = goDownName
            }
            if (!TextUtils.isEmpty(toDateRequested)) {
                toDateRequestedBy.text = toDateRequested
            }
        }
    }

    private fun updateCropList() {
        prodCropResponseModel = DatabaseManager.getProductCropDetails(this)
        val prpdCropListNew = DatabaseManager.getProductCropDetailProductNew(this)
        prpdCropListNew?.forEach {
            val bean = CropBean()
            bean.setcID(it.cropId.toInt())
            bean.item = it.crop
            if (!checkValue(bean.getcID()!!))
                cropList.add(bean)

        }
    }

    private fun checkValue(id: Int): Boolean {
        productList.forEach {
            if (id == it.getpID()) {
                return true
            }
        }
        return false
    }

    private fun proceedApply() {
        val intent = Intent()
        val b = Bundle()
        b.putString("crop_name", crop)
        b.putString("product_name", product)
        b.putString("needed_start", fromDateNeeded)
        b.putString("needed_end", toDateNeeded)
        b.putString("godown_name", goDownName)
        b.putString("requested_from", fromDateRequested)
        b.putString("requested_to", toDateRequested)
        b.putInt("crop_id", crop_id)
        b.putInt("product_id", product_id)
        b.putInt("godown_id", goDown_id)
        intent.putExtra("bundle", b)
        setResult(RESULT_OK, intent)
        finish()
    }

    private fun resetEverything() {
        crop = ""
        product = ""
        fromDateNeeded = ""
        toDateNeeded = ""
        goDownName = ""
        fromDateRequested = ""
        toDateRequested = ""
        crop_id = 0
        product_id = 0
        goDown_id = 0
        fromDate.text = "Start Date"
        toDate.text = "End Date"
        fromDateRequestedBy.text = "Start Date"
        toDateRequestedBy.text = "End Date"
        productName.text = "Select Product"
        cropName.text = "Select Crop"
    }

    private fun launchDateicker(i: Int) {
        val bundle = Bundle()
        bundle.putInt("key", i)
        val newFragment = SelectDateFragment()
        newFragment.arguments = bundle
        newFragment.show(this@FilterDemoRequestActivity.supportFragmentManager, "DatePicker")
    }

    private fun setDataForSpinner() {

        productSpinner = ProductSpinnerDialog(this, productList, "Select Product")
        productSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                productName.text = item
                product = item
                product_id = productList[position].getpID()!!
            }
        })

        cropSpinner = CropSpinnerDialog(this, cropList, "Select Crop")
        cropSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                cropName.text = item
                crop = item
                crop_id = cropList[position].getcID() ?: 0
                updateProductList()
            }
        })

        GlobalScope.launch {
            var tempData = ArrayList<GoDownData>()
            withContext(Dispatchers.IO) {
                tempData = ((appDataBaseClient?.goDownDataDao()?.getAllGoDownData()
                    ?: ArrayList<GoDownData>()) as ArrayList<GoDownData>)
            }.run {
                withContext(Dispatchers.Main) {
                    goDownList.clear()
                    goDownList.addAll(tempData)
                    goDownList.forEach() {
                        goDownListString.add(it.godown)
                    }
                    goDownSpinner = NumberSpinner(
                        this@FilterDemoRequestActivity,
                        goDownListString,
                        "Select GoDown"
                    )
                    goDownSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
                        override fun onClick(item: String, position: Int) {
                            goDown.text = item
                            goDownName = item
                            goDown_id = goDownList[position].godown_id
                        }
                    })
                }
            }
        }
    }

    private fun updateProductList() {
        productList.clear()
        prodCropResponseModel.forEach {
            if (it.cropId.equals(crop_id.toString())) {
                val product = ProductBean()
                product.setpID(it.productId.toInt())
                product.item = it.product
                productList.add(product)
                productSpinner?.updatelist(productList)
            }
        }
    }

    private fun setListeners() {
        toDateRequestedBy.setOnClickListener(this)
        fromDateRequestedBy.setOnClickListener(this)
        goDown.setOnClickListener(this)
        toDate.setOnClickListener(this)
        fromDate.setOnClickListener(this)
        productName.setOnClickListener(this)
        cropName.setOnClickListener(this)
        apply.setOnClickListener(this)
        reset.setOnClickListener(this)
        iCross.setOnClickListener(this)
    }

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.cropName -> {
                cropSpinner?.let {
                    if (!it.isEnable)
                        it.showSpinerDialog()
                }
            }
            R.id.productName -> {
                if (!TextUtils.isEmpty(crop)) {
                    productSpinner?.let {
                        if (!it.isEnable)
                            it.showSpinerDialog()
                    }
                } else {
                    makeToast("Please select crop first.")
                }
            }
            R.id.fromDate -> {
                fromDateNeeded = ""
                launchDateicker(2)
            }
            R.id.toDate -> {
                if (!fromDateNeeded.isNullOrEmpty()) {
                    toDateNeeded = ""
                    launchDateicker(3)
                } else
                    makeToast("Please Select Start Date")
            }
            R.id.goDown -> {
                goDownSpinner?.let {
                    if (!it.isEnable) {
                        it.showSpinerDialog()
                    }
                }
            }
            R.id.fromDateRequestedBy -> {
                fromDateRequested = ""
                launchDateicker(4)
            }
            R.id.toDateRequestedBy -> {
                if (!fromDateRequested.isNullOrEmpty()) {
                    toDateRequested = ""
                    launchDateicker(5)
                } else
                    makeToast("Please Select Start Date")
            }
            R.id.reset -> {
                resetEverything()
            }
            R.id.apply -> {
                proceedApply()
            }
            R.id.iCross -> {
                finish()
            }
        }
    }

    override fun onStartDateSelected(startDate: String, value: Int) {
        if (value == 2) {
            fromDateNeeded = startDate
            fromDate.text = fromDateNeeded
        } else if (value == 4) {
            fromDateRequested = startDate
            fromDateRequestedBy.text = fromDateRequested
        }
    }

    override fun onEndDateSelected(startDate: String, value: Int) {
        if (value == 3) {
            toDateNeeded = startDate
            toDate.text = toDateNeeded
        } else if (value == 5) {
            toDateRequested = startDate
            toDateRequestedBy.text = toDateRequested
        }
    }
}
