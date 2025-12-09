package com.dhanuka.indhan.PlanSyllabusForBdo.View

import android.content.DialogInterface
import android.os.Bundle
import android.view.View
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.LoginActivity
import com.dhanuka.indhan.Activity.NewTicketMVP.CropSpinnerDialog
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.CropBean
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.ProductBean
import com.dhanuka.indhan.Activity.NewTicketMVP.ProductSpinnerDialog
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.DataBase.DbStructure.MyContentObserver
import com.dhanuka.indhan.DataBase.TableDataModel.ProductCropResponse
import com.dhanuka.indhan.DataBase.TableDataModel.UserTeamModel
import com.dhanuka.indhan.DataBase.TableStructureModels.UserTeamTable
import com.dhanuka.indhan.DataSyncServices.DataSyncManager
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInterfaceFactory
import com.dhanuka.indhan.PlanSyllabusForBdo.Adapter.PlanSyllabusRCAdapter
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.BDOSpinner
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.PlanSyllabusForBdo.Model.SyllabusProduct
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.OnSpinerItemClick
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.activity_plan_syllabus.*

/**
 * Created by John on 5/7/2018.
 */
class PlanSyllabusActivity : BaseActivity(), MyContentObserver.DbValueChangeListener,
    SyncServiceInterfaceFactory.FailureListener, PlanSyllabusRCAdapter.PlanSyllabusClickListener {
    override fun onFailure(msg: String) {
        hideProgress()
    }

    override fun onLongClick(view: View, atpDbModel: SyllabusProduct, position: Int) {
        androidx.appcompat.app.AlertDialog.Builder(this)
            .setTitle(getResources().getString(R.string.nav_item_delete))
            .setMessage(getResources().getString(R.string.txt_delete_comment))
            .setPositiveButton("Yes", DialogInterface.OnClickListener { dialog, which ->
                DatabaseManager.deleteSyllabusofId(this, atpDbModel)
                updateView()
            })
            .setNegativeButton("No", DialogInterface.OnClickListener { dialog, which -> })
            .show()
    }

    override fun onDBSyncSuccess() {
        hideProgress()
    }


    private lateinit var productSpinnerDialog: ProductSpinnerDialog
    private lateinit var preference: Preference
    private var product1RecordResponseModel: ArrayList<ProductBean> = ArrayList()
    private var prodCropResponseModel: ArrayList<ProductCropResponse.ProductCropList> = ArrayList();
    private var cropRecordResponseModel: ArrayList<CropBean> = ArrayList()
    private lateinit var bdolist: ArrayList<UserTeamModel>
    private lateinit var syllabusProduct: ArrayList<SyllabusProduct>
    private lateinit var cropSpinnerDialog: CropSpinnerDialog
    private lateinit var ddspinner: NumberSpinner
    private lateinit var monthSpinner: NumberSpinner
    private lateinit var no_of_days_spinner: NumberSpinner
    private lateinit var bdo_spinner: BDOSpinner
    private var product1Id: Int = 0
    private lateinit var product1Name: String
    private var cropId: Int = 0
    private lateinit var cropName: String
    private var no_of_DD: String? = null
    private var no_of_Days: String? = null
    private lateinit var bdo_name: String
    private var month: String? = null
    private var bdo_id: Int = 0
    lateinit var adapter: PlanSyllabusRCAdapter

    private lateinit
    var token: String
    var counter = 0

    private lateinit var bdoObserver: MyContentObserver

    /**
     * Selected data for RecyclerView
     */

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_plan_syllabus)
        product1RecordResponseModel = ArrayList()
        syllabusProduct = ArrayList()
        preference = Preference(this)
        syllabusProduct = DatabaseManager.getAllSyllabusList(this, 0)
        bdolist = ArrayList()
        bdolist = DatabaseManager.getAllUserTeamDataList(this)
        // updateProductList()
        updateCropList()
        adapter = PlanSyllabusRCAdapter(syllabusProduct, this)
        adapter.setLongClickListener(this)
        rvListProductList.layoutManager =
            LinearLayoutManager(this)
        rvListProductList.adapter = adapter
        spinnerofProduct.setOnClickListener(this)
        spinnerCrop.setOnClickListener(this)
        spinnerBDOName.setOnClickListener(this)
        cropSpinnerDialog =
            CropSpinnerDialog(this, cropRecordResponseModel, "Select or Search Crop")
        cropSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerCrop.text = item
                cropId = cropRecordResponseModel[position].getcID()!!
                cropName = cropRecordResponseModel[position].item.toString()

                /*   cropId = cropRecordResponseModel[position].getcID()!!
                   cropName =cropRecordResponseModel[position].item.toString()*/
                //   updateCropBeanList(product1Id)
                updateProductList(cropId)
                spinnerofProduct.text = "Select Product"
                product1Id = 0
                product1Name = ""
            }
        })
        exportdb_btn.setOnClickListener(this)
        bdo_spinner = BDOSpinner(this, bdolist, "Select your BDO")
        bdo_spinner.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerBDOName.text = item
                bdo_id = bdolist[position].id
                bdo_name = bdolist[position].username.toString()
            }
        })
        productSpinnerDialog =
            ProductSpinnerDialog(this, product1RecordResponseModel, "Select or Search Product")
        productSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                // spinnerProduct.text = item
                spinnerofProduct.text = item
                product1Id = product1RecordResponseModel[position].getpID()!!
                product1Name = product1RecordResponseModel[position].item.toString()
                //   updateCropBeanList(product1Id)
                //  spinnerCrop.text="Select Crop"
                //  cropId=0
                //   cropName=""
            }
        })
        spinner_no_of_dd.setOnClickListener(this)
        ddspinner = NumberSpinner(this, Utility.getList(10), "Select no. of DD")
        ddspinner.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                no_of_DD = item
                spinner_no_of_dd.text = item
            }
        })
        spinner_no_of_days.setOnClickListener(this)
        no_of_days_spinner = NumberSpinner(this, Utility.getList(100), "Select no. of Activities")
        no_of_days_spinner.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                no_of_Days = item
                spinner_no_of_days.text = item
            }
        })
        spinnerMonth.setOnClickListener(this)
        iCross.setOnClickListener(this)
        save.setOnClickListener(this)
        monthSpinner = NumberSpinner(this, Utility.monthList, "Select Month")
        monthSpinner.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                month = item
                spinnerMonth.text = item
            }
        })

        tvReAddNewPhone.setOnClickListener(this)
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            val manager = DataSyncManager(this, this)
            token = Preference.getInstance(this).getString("token")
            if (!token.isNullOrEmpty()) {
                manager.getPlanActivity(token)
                showProgress()
            }
        }
        bdoObserver = MyContentObserver()
        bdoObserver.setListener(this)
        restoreDefault()
        applicationContext.contentResolver
            .registerContentObserver(UserTeamTable.CONTENT_URI, true, bdoObserver)
    }

    private fun updateCropList() {
        prodCropResponseModel = DatabaseManager.getProductCropDetails(this)
        val prpdCropListNew = DatabaseManager.getProductCropDetailProductNew(this)
        if (prpdCropListNew != null) {
            prpdCropListNew.forEach {
                var bean = CropBean()
                bean.setcID(it.cropId.toInt())
                bean.item = it.crop
                if (!checkValue(bean.getcID()!!))
                    cropRecordResponseModel.add(bean)

            }


        }
    }

    private fun checkValue(id: Int): Boolean {
        product1RecordResponseModel.forEach {
            if (id == it.getpID()) {
                return true
            }
        }
        return false
    }

    private fun updateCropBeanList(id: Int) {
        cropRecordResponseModel.clear()
        prodCropResponseModel.forEach {
            if (it.productId.equals(id.toString())) {
                var crop = CropBean()
                crop.setcID(it.cropId.toInt())
                crop.item = it.crop
                cropRecordResponseModel.add(crop)
                // cropRecordResponseModel.sort()
            }
        }
        /* cropRecordResponseModel = DatabaseManager.getCropDataFromDB(this);*/


        cropSpinnerDialog.updatelist(cropRecordResponseModel)
        cropSpinnerDialog.showSpinerDialog()
    }

    private fun updateProductList(id: Int) {
        product1RecordResponseModel.clear()
        prodCropResponseModel.forEach {
            if (it.cropId.toString().equals(id.toString())) {
                var product = ProductBean()
                product.setpID(it.productId.toInt())
                product.item = it.product
                product1RecordResponseModel.add(product)

            }
        }
        /* cropRecordResponseModel = DatabaseManager.getCropDataFromDB(this);*/
        productSpinnerDialog.updatelist(product1RecordResponseModel)
        productSpinnerDialog.showSpinerDialog()
    }

    override fun onClick(view: View) {
        when (view.id) {
            R.id.spinnerofProduct -> {
                if (!productSpinnerDialog.isEnable)
                    productSpinnerDialog.showSpinerDialog()
            }
            R.id.spinnerCrop -> {
                if (!cropSpinnerDialog.isEnable)
                    cropSpinnerDialog.showSpinerDialog()
            }
            R.id.spinner_no_of_days -> {
                if (!no_of_days_spinner.isEnable)
                    no_of_days_spinner.showSpinerDialog()
            }
            R.id.spinner_no_of_dd -> {
                if (!ddspinner.isEnable)
                    ddspinner.showSpinerDialog()
            }
            R.id.spinnerBDOName -> {
                if (!bdo_spinner.isEnable)
                    bdo_spinner.showSpinerDialog()
            }
            R.id.spinnerMonth -> {
                if (!monthSpinner.isEnable)
                    monthSpinner.showSpinerDialog()
            }
            R.id.tvReAddNewPhone -> {
                savePlan(false)
            }
            R.id.exportdb_btn -> {
                // exportDB()
            }
            R.id.iCross -> {
                finish()
            }
            R.id.save -> {
                savePlan(true)
            }
        }
    }

    private fun savePlan(res: Boolean) {
        if (checkvalues()) {
            counter++
            var product = SyllabusProduct()
            product.product_id = product1Id
            product.product_name = product1Name
            product.crop_id = cropId
            product.crops_name = cropName
            product.no_of_days = Integer.parseInt(no_of_Days!!)
            product.no_of_dd = Integer.parseInt(no_of_DD!!)
            product.bdo_id = bdo_id
            product.bdo_name = bdo_name
            product.month = month
            product.timestamp = System.currentTimeMillis().toString()
            var rid = preference.getString("ecn") + bdo_id + System.currentTimeMillis().toString()
            product.record_id = rid
            product.isUploaded = 0
            syllabusProduct.add(product)
            adapter.notifyDataSetChanged()
            DatabaseManager.saveSyllabus(this, product)
            restoreDefault()
            if (res) finish()
        }
    }

    private fun restoreDefault() {
        product1Id = 0
        product1Name = ""
        cropId = 0
        cropName = ""
        /*  bdo_id = 0
          bdo_name = ""*/
        no_of_Days = ""
        no_of_DD = ""
        /*month = ""*/
        spinnerofProduct.text = "Select Product"
        spinnerCrop.text = "Select Crop"
        spinner_no_of_days.text = "Select No. of Activities"
        spinner_no_of_dd.text = "Select No. of DD"
        /*spinnerBDOName.text = "Select BDO Name"
        spinnerMonth.text = "Select Month"*/
        updateView()

    }

    private fun updateView() {
        var sProduct = DatabaseManager.getAllSyllabusList(this, 0)
        syllabusProduct.clear()
        syllabusProduct.addAll(sProduct)
        adapter.notifyDataSetChanged()
    }

    private fun checkvalues(): Boolean {
        if (bdo_id == 0 || bdo_name.isNullOrEmpty()) {
            makeToast("Please Select BDO Name")
            return false
        }
        if (month.isNullOrEmpty()) {
            makeToast("Please Select Month")
            return false
        }
        if (no_of_Days.isNullOrEmpty()) {
            makeToast("Please Select no. of Activities")
            return false
        }

        if (product1Id == 0 || product1Name.isNullOrEmpty()) {
            makeToast("Please Select Product")
            return false
        }
        if (cropId == 0 || cropName.isNullOrEmpty()) {
            makeToast("Please Select Crop")
            return false
        }

        if (no_of_DD.isNullOrEmpty()) {
            makeToast("Please Select no. of DD")
            return false
        }
        return true
    }

    override fun onDestroy() {
        super.onDestroy()
        /*getApplicationContext().getContentResolver().unregisterContentObserver(cropObservable)
        getApplicationContext().getContentResolver().unregisterContentObserver(productObservable)*/
        getApplicationContext().getContentResolver().unregisterContentObserver(bdoObserver)
    }


    override fun onInvalidAttempt() {
        hideProgress()
        preference.putString("ticketId", "")
        preference.putString("ticketTime", "")
        preference.putBolean("isLogin", false)
        preference.putString("token", "")
        launchIntent(LoginActivity::class.java, true)
    }

    override fun OnDbValueChange() {
        runOnUiThread(Runnable {
            /*  cropRecordResponseModel = DatabaseManager.getCropDataFromDB(this);
              cropSpinnerDialog.updatelist(cropRecordResponseModel)*/
            bdolist = DatabaseManager.getAllUserTeamDataList(this)
            bdo_spinner.updateList(bdolist)
            /* product1RecordResponseModel = DatabaseManager.getProductDataFromDB(this)
             productSpinnerDialog.updatelist(product1RecordResponseModel)*/

        })
    }
}
