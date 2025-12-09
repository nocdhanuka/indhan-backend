package com.dhanuka.indhan.SyllabusHistory.Dialog

import android.app.AlertDialog
import android.content.Intent
import android.os.Bundle
import androidx.appcompat.app.AppCompatActivity
import android.util.DisplayMetrics
import android.view.View
import android.widget.Toast
import com.dhanuka.indhan.Activity.NewTicketMVP.CropSpinnerDialog
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.CropBean
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.ProductBean
import com.dhanuka.indhan.Activity.NewTicketMVP.ProductSpinnerDialog
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.DataBase.TableDataModel.ProductCropResponse
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.OnSpinerItemClick
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.dialog_add_product_layout.*


/**
 * Created by John on 5/16/2018.
 */
class AddProductDialogActivity : AppCompatActivity(), View.OnClickListener {

    internal lateinit var onSpinerItemClick: OnSpinerItemClick
    internal lateinit var alertDialog: AlertDialog
    internal var pos: Int = 0
    internal var style: Int = 0

    lateinit var cropSpinner: CropSpinnerDialog
    lateinit var prodSpinner: ProductSpinnerDialog
    lateinit var numberSpinner: NumberSpinner

    var cropId: Int = 0
    var cropName: String = ""
    var prodId: Int = 0
    var prodName: String = ""
    var no_OfDays: Int = 0
    private var productRecordResponseModel: ArrayList<ProductBean> = ArrayList()
    private var prodCropResponseModel: ArrayList<ProductCropResponse.ProductCropList> = ArrayList();
    private var cropRecordResponseModel: ArrayList<CropBean> = ArrayList()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.dialog_add_product_layout)
        spinner_crop.setOnClickListener(this)
        spinnerDays.setOnClickListener(this)
        spinnerProductName.setOnClickListener(this)
        submit.setOnClickListener(this)
        val dm = DisplayMetrics()
        windowManager.defaultDisplay.getMetrics(dm)
        val width = dm.widthPixels
        val height = dm.heightPixels
        window.setLayout((width * .9).toInt(), (height * .7).toInt())
        //    updateProductList()
        updateCropList()

    }

    /*  private fun updateProductList() {
          prodCropResponseModel = DatabaseManager.getProductCropDetails(this)
          prodSpinner= ProductSpinnerDialog(this,productRecordResponseModel,"Select BDO"  )
          prodSpinner.bindOnSpinerListener(object : OnSpinerItemClick{
              override fun onClick(item: String, position: Int) {
                  //  bdo_id=Integer.parseInt(item)
                  spinnerProductName.text=item
                  prodName=item
                  prodId=productRecordResponseModel.get(position).getpID()!!
                  updateCropBeanList(prodId)
              }
          })
          cropSpinner= CropSpinnerDialog(this,cropRecordResponseModel,"Select BDO"  )
          cropSpinner.bindOnSpinerListener(object : OnSpinerItemClick{
              override fun onClick(item: String, position: Int) {
                  //  bdo_id=Integer.parseInt(item)
                  spinner_crop.text=item
                  cropName=item
                  cropId=cropRecordResponseModel.get(position).getcID()!!
              }
          })
          if (prodCropResponseModel != null) {
              prodCropResponseModel.forEach {
                  var bean = ProductBean()
                  bean.setpID(it.productId.toInt())
                  bean.item = it.product
                  if (!checkValue(bean.getpID()!!))
                      productRecordResponseModel.add(bean)
              }
          }
      }*/
    private fun updateCropList() {
        prodCropResponseModel = DatabaseManager.getProductCropDetails(this)
        var prodNewCropProductList = DatabaseManager.getProductCropDetailProductNew(this)

        prodSpinner = ProductSpinnerDialog(this, productRecordResponseModel, "Select Product")
        prodSpinner.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                //  bdo_id=Integer.parseInt(item)
                spinnerProductName.text = item
                prodName = item
                prodId = productRecordResponseModel.get(position).getpID()!!

            }
        })

        cropSpinner = CropSpinnerDialog(this, cropRecordResponseModel, "Select Crop")
        cropSpinner.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                //  bdo_id=Integer.parseInt(item)
                spinner_crop.text = item
                cropName = item
                cropId = cropRecordResponseModel.get(position).getcID()!!
                //  updateCropBeanList(prodId)
                updateProductBeanList(cropId)
            }
        })
        if (prodNewCropProductList != null) {
            prodNewCropProductList.forEach {
                var bean = CropBean()
                bean.setcID(it.cropId.toInt())
                bean.item = it.crop
                if (!checkValue(bean.getcID()!!))
                    cropRecordResponseModel.add(bean)
            }
        }
    }

    private fun checkValue(id: Int): Boolean {
        productRecordResponseModel.forEach {
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
            }
        }
        /* cropRecordResponseModel = DatabaseManager.getCropDataFromDB(this);*/
        cropSpinner.updatelist(cropRecordResponseModel)
        cropSpinner.showSpinerDialog()
    }

    private fun updateProductBeanList(id: Int) {
        productRecordResponseModel.clear()
        prodCropResponseModel.forEach {
            if (it.cropId.equals(id.toString())) {
                var productBean = ProductBean()
                productBean.setpID(it.productId.toInt())
                productBean.item = it.product
                productRecordResponseModel.add(productBean)
            }
        }
        /* cropRecordResponseModel = DatabaseManager.getCropDataFromDB(this);*/
        prodSpinner.updatelist(productRecordResponseModel)
        prodSpinner.showSpinerDialog()
    }

    override fun onClick(view: View) {
        when (view.id) {
            R.id.spinner_crop -> {

                //var items= DatabaseManager.getCropDataFromDB(this)

                cropSpinner.showSpinerDialog()
            }
            R.id.spinnerProductName -> {

                //var prod_items=DatabaseManager.getProductDataFromDB(this)

                prodSpinner.showSpinerDialog()
            }
            R.id.spinnerDays -> {
                numberSpinner =
                    NumberSpinner(this, Utility.getList(100), "Select no. of Activities")

                numberSpinner.bindOnSpinerListener(object : OnSpinerItemClick {
                    override fun onClick(item: String, position: Int) {
                        spinnerDays.text = item
                        no_OfDays = Integer.parseInt(item)
                    }
                })
                numberSpinner.showSpinerDialog()
            }
            R.id.submit -> {
                if (cropId == 0) {
                    Toast.makeText(this, "Please Select Crop", Toast.LENGTH_SHORT).show()
                } else if (no_OfDays == 0) {
                    Toast.makeText(this, "Please Select no. of Days", Toast.LENGTH_SHORT).show()
                } else if (prodId == 0) {
                    Toast.makeText(this, "Please Select Product", Toast.LENGTH_SHORT).show()
                } else {
                    val intent = Intent()
                    intent.putExtra("crop_id", cropId)
                    intent.putExtra("crop_name", cropName)
                    intent.putExtra("prod_id", prodId)
                    intent.putExtra("prod_name", prodName)
                    intent.putExtra("nod", no_OfDays)
                    setResult(1001, intent)
                    finish()
                }
            }
        }
    }
}
