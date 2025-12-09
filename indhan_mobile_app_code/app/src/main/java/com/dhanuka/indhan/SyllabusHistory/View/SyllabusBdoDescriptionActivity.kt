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
import com.dhanuka.indhan.PlanSyllabusForBdo.Adapter.BDODescriptordapter
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.PlanSyllabusForBdo.Model.SyllabusProduct
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.SyllabusHistory.Dialog.AddProductDialogActivity
import com.dhanuka.indhan.util.OnSpinerItemClick
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.activity_syllabus_bdo_description.*

/**
 * Created by John on 5/15/2018.
 */
class SyllabusBdoDescriptionActivity : BaseActivity(),
    BDODescriptordapter.BDODescriptorClickListener {


    private lateinit var no_of_days_spinner: NumberSpinner
    lateinit var cropSpinnerDialog: CropSpinnerDialog
    private lateinit var syllabusResponseModel: ArrayList<SyllabusProduct>
    var bdoID: Int = 0
    var bdoName: String = ""
    var syllabusList: ArrayList<SyllabusProduct> = ArrayList()
    var month: String = ""
    lateinit var preference: Preference

    private lateinit var adapter: BDODescriptordapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_syllabus_bdo_description)
        submit.visibility = View.GONE
        val bundle = intent.extras
        if (bundle != null) {
            bdoID = bundle.getInt("bdo_id")
            bdoName = bundle.getString("bdo_name")!!
            month = bundle.getString("month")!!
        }
        updateProductList()
        preference = Preference(this)
        pd_name.text = bdoName
        rcViewProdDes.layoutManager =
            LinearLayoutManager(this)
        syllabusResponseModel = DatabaseManager.getAllSyllabusListWhereBdoID(this, bdoID, month, 0)
        updateSyllabusList()
        iCross.setOnClickListener(this)
    }

    private var prodCropResponseModel: ArrayList<ProductCropResponse.ProductCropList> = ArrayList();
    private fun updateProductList() {
        prodCropResponseModel = DatabaseManager.getProductCropDetails(this)
    }

    fun updateSyllabusList() {
        syllabusList.clear()
        syllabusList = DatabaseManager.getAllSyllabusListWhereBdoID(this, bdoID, month, 0)
        adapter = BDODescriptordapter(syllabusList, this)
        adapter.setClickListener(this)
        rcViewProdDes.adapter = adapter
        adapter.notifyDataSetChanged()
    }

    override fun onClick(view: View) {
        when (view.id) {
            R.id.iCross -> {
                finish()
            }
        }
    }

    override fun onAddClick() {
        val intent = Intent(this, AddProductDialogActivity::class.java)
        startActivityForResult(intent, 1001)
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == 1001) {
            if (data != null) {
                var crop_id = data.getIntExtra("crop_id", 0)
                var crop_name = data.getStringExtra("crop_name")
                var prod_name = data.getStringExtra("prod_name")
                var no_ofDays = data.getIntExtra("nod", 0)
                var prod_id = data.getIntExtra("prod_id", 0)
                var product = SyllabusProduct()
                product.product_id = prod_id
                product.product_name = prod_name
                product.crop_id = crop_id
                product.crops_name = crop_name
                product.no_of_days = no_ofDays
                product.no_of_dd = 0
                product.bdo_id = bdoID
                product.bdo_name = bdoName
                product.month = month
                product.timestamp = System.currentTimeMillis().toString()
                var rid =
                    preference.getString("ecn") + bdoID + System.currentTimeMillis().toString()
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
        no_of_days_spinner = NumberSpinner(this, Utility.getList(100), "Select no. of Activities ")
        no_of_days_spinner.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                prod.no_of_days = Integer.parseInt(item)
                DatabaseManager.checkAndUpdateSyllabusDatabase(applicationContext, prod)
                updateSyllabusList()
            }
        })
        no_of_days_spinner.showSpinerDialog()
    }


    override fun onUpdateCrop(prod: SyllabusProduct) {
        val cropList: ArrayList<CropBean> = ArrayList()
        prodCropResponseModel.forEach {
            if (it.productId.toInt() == prod.product_id) {
                var cropbean = CropBean()
                cropbean.setcID(it.cropId.toInt())
                cropbean.item = it.crop
                cropList.add(cropbean)
            }
        }
        cropSpinnerDialog = CropSpinnerDialog(this, cropList, "Select crop")
        cropSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                prod.crops_name = item
                prod.crop_id = cropList.get(position).getcID()!!
                DatabaseManager.checkAndUpdateSyllabusDatabase(applicationContext, prod)
                updateSyllabusList()
            }
        })
        cropSpinnerDialog.showSpinerDialog()
    }

}
