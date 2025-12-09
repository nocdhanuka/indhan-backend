package com.dhanuka.indhan.SyllabusHistory.View

import android.content.DialogInterface
import android.content.Intent
import android.os.Bundle
import androidx.recyclerview.widget.LinearLayoutManager
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.NewTicketMVP.CropSpinnerDialog
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.CropBean
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.DataBase.TableDataModel.ProductCropResponse
import com.dhanuka.indhan.PlanSyllabusForBdo.Adapter.ProductDescriptorAdapter
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.PlanSyllabusForBdo.Model.SyllabusProduct
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.SyllabusHistory.Dialog.AddBDODialogActivity
import com.dhanuka.indhan.util.OnSpinerItemClick
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.activity_syllabus_product_description.*

/**
 * Created by John on 5/15/2018.
 */
class SyllabusProductDescriptionActivity : BaseActivity(),
    ProductDescriptorAdapter.ProductDescriptorClickListener {

    private lateinit var no_of_days_spinner: NumberSpinner

    lateinit var cropSpinnerDialog: CropSpinnerDialog
    lateinit var preference: Preference
    private var cropRecordResponseModel: ArrayList<CropBean> = ArrayList()
    var cropId: Int = 0
    var cropName: String = ""
    var productID: Int = 0
    var productName: String = ""
    var syllabusList: ArrayList<SyllabusProduct> = ArrayList()
    var month: String = ""

    private lateinit var adapter: ProductDescriptorAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_syllabus_product_description)
        val bundle = intent.extras
        test.visibility = View.GONE
        if (bundle != null) {
            productID = bundle.getInt("product_id")
            productName = bundle.getString("product_name")!!
            month = bundle.getString("month")!!
        }
        preference = Preference(this)
        pd_name.text = productName
        rcViewProdDes.layoutManager =
            LinearLayoutManager(this)
        updateProductList()
        //cropRecordResponseModel = DatabaseManager.getCropDataFromDB(this)
        spinnerCrop.setOnClickListener(this)
        iCross.setOnClickListener(this)
        cropSpinnerDialog =
            CropSpinnerDialog(this, cropRecordResponseModel, "Select or Search Crop")
        cropSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerCrop.text = item
                cropId = cropRecordResponseModel[position].getcID()!!
                cropName = cropRecordResponseModel[position].item.toString()
                updateSyllabusList()
            }
        })
    }

    private var prodCropResponseModel: ArrayList<ProductCropResponse.ProductCropList> = ArrayList();
    private fun updateProductList() {
        prodCropResponseModel = DatabaseManager.getProductCropDetailProductNew(this)
        prodCropResponseModel.forEach {
            if (it.productId.toInt() == productID) {
                var cropbean = CropBean()
                cropbean.setcID(it.cropId.toInt())
                cropbean.item = it.crop
                cropRecordResponseModel.add(cropbean)
            }
        }
    }

    fun updateSyllabusList() {
        syllabusList.clear()
        syllabusList =
            DatabaseManager.getSyllabusFromCropAndProduct(this, cropId, productID, month, 0)
        adapter = ProductDescriptorAdapter(syllabusList, this)
        adapter.setClickListener(this)
        rcViewProdDes.adapter = adapter
        adapter.notifyDataSetChanged()
    }

    override fun onClick(view: View) {
        when (view.id) {
            R.id.spinnerCrop -> {
                cropSpinnerDialog.showSpinerDialog()
            }
            R.id.iCross -> {
                finish()
            }
        }
    }

    override fun onAddClick() {
        val intent = Intent(this, AddBDODialogActivity::class.java)
        startActivityForResult(intent, 1001)
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == 1001) {
            if (data != null) {
                var bdo_id = data.getIntExtra("bdo_id", 0)
                var no_ofDays = data.getIntExtra("nod", 0)
                var bdo_Name = data.getStringExtra("bdo_name")
                var test: SyllabusProduct = SyllabusProduct()
                var product = SyllabusProduct()
                product.product_id = productID
                product.product_name = productName
                product.crop_id = cropId
                product.crops_name = cropName
                product.no_of_days = no_ofDays
                product.no_of_dd = 0
                product.bdo_id = bdo_id
                product.bdo_name = bdo_Name
                product.month = month
                product.timestamp = System.currentTimeMillis().toString()
                var rid =
                    preference.getString("ecn") + bdo_id + System.currentTimeMillis().toString()
                product.record_id = rid
                product.isUploaded = 0
                DatabaseManager.saveSyllabus(this, product)
                updateSyllabusList()
            }
        }
    }

    override fun onDeleteSyllabusfromBDO(prod: SyllabusProduct) {
        androidx.appcompat.app.AlertDialog.Builder(this)
            .setTitle(getResources().getString(R.string.nav_item_delete))
            .setMessage(getResources().getString(R.string.txt_delete_comment))
            .setPositiveButton("Yes", DialogInterface.OnClickListener { dialog, which ->
                DatabaseManager.deleteSyllabusofId(applicationContext, prod)
                updateSyllabusList()
            })
            .setNegativeButton("No", DialogInterface.OnClickListener { dialog, which -> })
            .show()
    }

    override fun onUpdateNoOfDays(prod: SyllabusProduct) {
        no_of_days_spinner = NumberSpinner(this, Utility.getList(100), "Select no. of Activities")
        no_of_days_spinner.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                prod.no_of_days = Integer.parseInt(item)
                DatabaseManager.checkAndUpdateSyllabusDatabase(applicationContext, prod)
                updateSyllabusList()
            }
        })
        no_of_days_spinner.showSpinerDialog()
    }
}
