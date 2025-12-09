package com.dhanuka.indhan.DemoRequestEvaluated.activities

import android.app.DatePickerDialog
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
import com.dhanuka.indhan.DemoRequestEvaluated.models.RequestMaterialModel
import com.dhanuka.indhan.MenuScreenActivity.OfflineDatabase
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.util.OnSpinerItemClick
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.demo_request_material_activity.*
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.GlobalScope
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import java.util.*

class DemoRequestMaterialsActivity : BaseActivity() {

    private lateinit var preference: Preference
    private var appDataBaseClient: AppDatabase? = null
    private var selectedDate: String = ""
    private var productSpinner: ProductSpinnerDialog? = null
    private var cropSpinner: CropSpinnerDialog? = null
    private var goDownSpinner: NumberSpinner? = null
    private var productList: ArrayList<ProductBean> = ArrayList()
    private var cropList: ArrayList<CropBean> = ArrayList()
    private var prodCropResponseModel: ArrayList<ProductCropResponse.ProductCropList> = ArrayList()
    private var goDownList: ArrayList<GoDownData> = ArrayList()
    private var goDownListString: ArrayList<String> = ArrayList()
    private var product_id = 0
    private var product = ""
    private var crop_id = 0
    private var crop = ""
    private var goDown_id = 0
    private var goDownName = ""

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.demo_request_material_activity)
        preference = Preference(this)
        appDataBaseClient = DatabaseClient.getInstance(this).appDatabase
        createDataDRopDown()
        setListener()
    }

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.txtFilterCrop -> {
                cropSpinner?.let {
                    if (!it.isEnable)
                        it.showSpinerDialog()
                }

            }
            R.id.txtFilterProduct -> {
                productSpinner?.let {
                    if (!it.isEnable)
                        it.showSpinerDialog()
                }
            }
            R.id.txtNeedBy -> {
                Utility.hideKeyboardFrom(this, txtNeedBy)
                showDatePicker()
            }
            R.id.txtFilterGoDown -> {
                goDownSpinner?.let {
                    if (!it.isEnable) {
                        it.showSpinerDialog()
                    }
                }
            }
            R.id.btnRbSubmit -> {
                Utility.hideKeyboardFrom(this, btnRbSubmit)
                if (validData()) {
                    saveDemoRequestMaterialDataInDb()
                }
            }
        }
    }

    private fun saveDemoRequestMaterialDataInDb() {
        val materialRequest = RequestMaterialModel()
        materialRequest.apiToken = preference.getString("token")
        materialRequest.crop_id = crop_id.toString()
        materialRequest.crop_name = crop
        materialRequest.product_id = product_id.toString()
        materialRequest.product_name = product
        materialRequest.godown_id = goDown_id.toString()
        materialRequest.godown_name = goDownName
        materialRequest.expected_demos = txtEstimatedDemo.text.toString().trim()
        materialRequest.need_by_date = selectedDate
        materialRequest.requested_units = txtRequiredUnit.text.toString().trim()
        materialRequest.isUploaded = false
        materialRequest.timeStamp = System.currentTimeMillis()
        GlobalScope.launch {
            withContext(Dispatchers.IO) {
                appDataBaseClient?.requestMaterialModelDao()?.insert(materialRequest)
            }.run {
                withContext(Dispatchers.Main) {
                    val offlineDatabase = OfflineDatabase(this@DemoRequestMaterialsActivity)
                    offlineDatabase.uploadRequestMaterialData()
                    makeToast(getString(R.string.request_data_uploaded_msg))
                    finish()
                }
            }
        }
    }

    private fun validData(): Boolean {
        when {
            TextUtils.isEmpty(crop) -> {
                makeToast("Please select crop to request material.")
                return false
            }
            crop_id == 0 -> {
                makeToast("Please select crop to request material.")
                return false
            }
            TextUtils.isEmpty(product) -> {
                makeToast("Please select product to request material.")
                return false
            }
            product_id == 0 -> {
                makeToast("Please select product to request material.")
                return false
            }
            TextUtils.isEmpty(txtEstimatedDemo.text.toString().trim()) -> {
                makeToast("Please enter estimated demo number.")
                return false
            }
            TextUtils.isEmpty(selectedDate) -> {
                makeToast("Please select estimated date.")
                return false
            }
            TextUtils.isEmpty(txtRequiredUnit.text.toString().trim()) -> {
                makeToast("Please enter required units in Kg/Ltr.")
                return false
            }
            goDown_id == 0 -> {
                makeToast("Please select GoDown for delivery.")
                return false
            }
            TextUtils.isEmpty(goDownName) -> {
                makeToast("Please select GoDown for delivery.")
                return false
            }
            else -> return true
        }
    }

    private fun setListener() {
        txtFilterCrop.setOnClickListener(this)
        txtFilterProduct.setOnClickListener(this)
        txtNeedBy.setOnClickListener(this)
        txtFilterGoDown.setOnClickListener(this)
        btnRbSubmit.setOnClickListener(this)
    }

    private fun createDataDRopDown() {
        productList.clear()
        cropList.clear()
        updateCropList()
        productSpinner = ProductSpinnerDialog(this, productList, "Select Product")
        productSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                txtFilterProduct.text = item
                product = item
                product_id = productList[position].getpID()!!
            }
        })

        cropSpinner = CropSpinnerDialog(this, cropList, "Select Crop")
        cropSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                txtFilterCrop.text = item
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
                        this@DemoRequestMaterialsActivity,
                        goDownListString,
                        "Select GoDown"
                    )
                    goDownSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
                        override fun onClick(item: String, position: Int) {
                            txtFilterGoDown.text = item
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

    private fun showDatePicker() {
        val c = Calendar.getInstance(Locale.ENGLISH)
        val year = c.get(Calendar.YEAR)
        val month = c.get(Calendar.MONTH)
        val day = c.get(Calendar.DAY_OF_MONTH)

        val mDatePicker = DatePickerDialog(
            activity,
            { view, year, monthOfYear, dayOfMonth ->

                // Display Selected date in textbox
                selectedDate = "" + year + "-" + (monthOfYear + 1) + "-" + dayOfMonth
                txtNeedBy.text = selectedDate

            },
            year,
            month,
            day
        )
        mDatePicker.datePicker.minDate = System.currentTimeMillis()

        // Subtract 3 months from Calendar current date
        c.add(Calendar.MONTH, 6)
        // Set the Calendar new date as minimum date of date picker
        mDatePicker.datePicker.maxDate = c.timeInMillis
        mDatePicker.show()
    }

    private fun checkValue(id: Int): Boolean {
        productList.forEach {
            if (id == it.getpID()) {
                return true
            }
        }
        return false
    }

}