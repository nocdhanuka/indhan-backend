package com.dhanuka.indhan.activities

import android.content.Intent
import android.os.Bundle
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.NewTicketMVP.*
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.CropBean
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.DataSourceBean
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.ProductBean
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.DataBase.TableDataModel.ProductCropResponse
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.TestimonialList.filter.DDBean
import com.dhanuka.indhan.acpfarmers.ACPFIlterBean
import com.dhanuka.indhan.util.OnGetId
import com.dhanuka.indhan.util.OnSpinerItemClick
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.activity_activities_filter.*
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*

class ActivitiesFilterActivity : BaseActivity() {
    override fun onClick(v: View?) {

    }


    private lateinit var preference: Preference
    private lateinit var dataSourceSpinnerDialog: DataSourceSpinnerDialog
    private lateinit var productSpinnerDialog: ProductSpinnerDialog
    private lateinit var cropSpinnerDialog: CropSpinnerDialog
    private lateinit var ddSpinnerFORFLDialog: DDSpinnerDialog

    //    private lateinit var flSpinnerDialog: FLSpinnerDialog
    private lateinit var flSpinnerDialog: FLNameDialog
    private lateinit var ddSpinnerDialog: DDFilterNameDialog
    private lateinit var asmNameDialog: ASMNameDialog
    private lateinit var rsmNameDialog: RSMNameDialog


    private val dataSourceRecordResponseModel = ArrayList<DataSourceBean>()
    private var prodCropResponseModel: ArrayList<ProductCropResponse.ProductCropList> = ArrayList()
    private var productList: ArrayList<ProductBean> = ArrayList()
    private var cropList: ArrayList<CropBean> = ArrayList()
    private var ddList: ArrayList<ACPFIlterBean.Data.DD> = ArrayList()
    private var flList: ArrayList<ACPFIlterBean.Data.BDO> = ArrayList()
    private var asmList: ArrayList<ACPFIlterBean.Data.ASM> = ArrayList()
    private var rsmList: ArrayList<ACPFIlterBean.Data.RSM> = ArrayList()
    private var ddListForFL: ArrayList<DDBean.Data> = ArrayList()


    private var startDate = ""
    private var endDate = ""
    private var crop = ""
    private var product = ""
    private var dd = ""
    private var fl = ""
    private var dataSourece = ""
    private var rsmId = ""
    private var asmId = ""
    private var type = ""
    private var ID = ""


    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_activities_filter)
        preference = Preference(this)



        if (preference.getString("user_type").equals("BDO", false)) {
            llASM.visibility = View.GONE
            llRSM.visibility = View.GONE
            spinnerFL.visibility = View.GONE
            getDDList()

        } else {
            getFilterList(preference.getString("token"))

        }

        /*
        * init dialog or spinner
        * */
        dataSourceSpinnerDialog = DataSourceSpinnerDialog(
            this,
            dataSourceRecordResponseModel,
            "Select or Search Data Source"
        )
        dataSourceSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerDataSource.text = dataSourceRecordResponseModel[position].item
                dataSourece = dataSourceRecordResponseModel[position].dsID.toString()


            }
        })


        productSpinnerDialog = ProductSpinnerDialog(this, productList, "Select Product")
        productSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerProduct.text = productList[position].item
                product = productList[position].item.toString()
            }
        })

        ddSpinnerFORFLDialog = DDSpinnerDialog(this, ddListForFL, "Select DD")
        ddSpinnerFORFLDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                if (ddList.size > 0) {
                    spinnerDD.text = ddList[position].name
                    dd = ddList[position].ddId
                }
            }
        })


        cropSpinnerDialog = CropSpinnerDialog(this, cropList, "Select Crop")
        cropSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                SpinnerCrop.text = cropList[position].item
                crop = cropList[position].item.toString()
            }
        })

        ddSpinnerDialog = DDFilterNameDialog(this, ddList, "Select DD", object : OnGetId {
            override fun onClick(item: String, position: Int, sId: String) {
                spinnerDD.text = ddList[position].name
                dd = ddList[position].ddId
            }

        })
        ddSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {


            }
        })

        flSpinnerDialog = FLNameDialog(this, flList, "Select FL", object : OnGetId {
            override fun onClick(item: String, position: Int, sId: String) {
                spinnerFL.text = flList[position].name
                fl = flList[position].id
                ID = flList[position].id
                type = "bdo"
                getFilterList(preference.getString("token"))
            }

        })
        flSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {

            }
        })


        asmNameDialog = ASMNameDialog(this, asmList, "Select ASM", object : OnGetId {
            override fun onClick(item: String, position: Int, sId: String) {
                txtASM.text = item
                asmId = sId
                ID = sId
                type = "sales_area"
                getFilterList(preference.getString("token"))

            }

        })

        rsmNameDialog = RSMNameDialog(this, rsmList, "Select RSM", object : OnGetId {
            override fun onClick(item: String, position: Int, sId: String) {
                txtRSM.text = item
                rsmId = sId
                ID = sId
                type = "sales_regional"
                getFilterList(preference.getString("token"))


            }

        })










        updateDB()
        updateProductList()
        updateCropBeanList(0)


        fromDate.setOnClickListener {
            Utility.getPickedDate(this, object : Utility.DatePickCallBack {
                override fun onCancel() {

                }

                override fun onDateSet(date: String?, formattedDate: String?) {
                    fromDate.text = date
                    startDate = date.toString()
                    //fromDate=date!!
                }
            })

        }

        toDate.setOnClickListener {
            Utility.getPickedDate(this, object : Utility.DatePickCallBack {
                override fun onCancel() {

                }

                override fun onDateSet(date: String?, formattedDate: String?) {
                    toDate.text = date
                    endDate = date.toString()
                }
            })

        }


        spinnerDataSource.setOnClickListener {
            if (!dataSourceSpinnerDialog.isEnable)
                dataSourceSpinnerDialog.showSpinerDialog()
        }

        spinnerDD.setOnClickListener {
            if (preference.getString("user_type").equals("BDO", false)) {
                if (!ddSpinnerFORFLDialog.isEnable)
                    ddSpinnerFORFLDialog.showSpinerDialog()
            } else {
                if (!ddSpinnerDialog.isEnable)
                    ddSpinnerDialog.showSpinerDialog()
            }
        }

        spinnerFL.setOnClickListener {
            if (!flSpinnerDialog.isEnable)
                flSpinnerDialog.showSpinerDialog()
        }

        spinnerProduct.setOnClickListener {
            if (!productSpinnerDialog.isEnable)
                productSpinnerDialog.showSpinerDialog()
        }

        SpinnerCrop.setOnClickListener {
            if (!cropSpinnerDialog.isEnable)
                cropSpinnerDialog.showSpinerDialog()
        }

        txtRSM.setOnClickListener {
            if (!rsmNameDialog.isEnable)
                rsmNameDialog.showSpinerDialog()
        }

        txtASM.setOnClickListener {
            if (!asmNameDialog.isEnable)
                asmNameDialog.showSpinerDialog()
        }

        btnClear.setOnClickListener {
            startDate = ""
            endDate = ""
            fl = ""
            dd = ""
            crop = ""
            product = ""
            dataSourece = ""

        }

        btnSubmit.setOnClickListener {
            val intent = Intent(this, callingActivity!!::class.java)
            intent.putExtra("startDate", startDate)
            intent.putExtra("endDate", endDate)
            intent.putExtra("product", product)
            intent.putExtra("crop", crop)
            intent.putExtra("fl", fl)
            intent.putExtra("dd", dd)
            intent.putExtra("dataSource", dataSourece)
            intent.putExtra("rsm", rsmId)
            intent.putExtra("asm", asmId)
            setResult(100, intent)
            finish()

        }


    }


    /*
    * get Data from db
    * */
    private fun updateDB() {
        val dataSource = DatabaseManager.getDataSourceFromDB(this)
        dataSourceRecordResponseModel.addAll(dataSource)
    }


    /*
* get product list
* */
    private fun updateProductList() {
        prodCropResponseModel = DatabaseManager.getProductCropDetails(this)
        if (prodCropResponseModel != null) {
            prodCropResponseModel.forEach {
                val bean = ProductBean()
                bean.setpID(it.productId.toInt())
                bean.item = it.product
                if (!checkValue(bean.getpID()!!))
                    productList.add(bean)
            }
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

    private fun updateCropBeanList(id: Int) {
        prodCropResponseModel.forEach {
            val crop = CropBean()
            crop.setcID(it.cropId.toInt())
            crop.item = it.crop
            cropList.add(crop)

        }
        /* cropRecordResponseModel = DatabaseManager.getCropDataFromDB(this);*/
        cropSpinnerDialog.updatelist(cropList)
        // cropSpinner.showSpinerDialog()
    }


    /*
    * get DD list
    * */
    private fun getDDList() {
        val model: String = preference.getString("token")
        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).getDDList(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<DDBean>() {
                override fun onCompleted() {}


                override fun onError(e: Throwable) {
                    hideProgress()

                }

                override fun onNext(listBean: DDBean) {
                    hideProgress()
                    if (listBean.statusCode == 200) {
                        ddListForFL.addAll(listBean.data)
                        ddSpinnerFORFLDialog.updatelist(ddListForFL)
                    }
                }
            })
    }


    /*
   * get DD list
   * */
    private fun getFLList() {

        val query = HashMap<String, Any>()
        query["api_token"] = preference.getString("token")

        RetrofitClient.mvpClient.create(ApiServiceRequest::class.java).flList(query)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<FLBean>() {
                override fun onCompleted() {}
                override fun onError(e: Throwable) {
                    hideProgress()
                }

                override fun onNext(flBean: FLBean) {
                    hideProgress()
                    if (flBean.statusCode == 200) {

                        //flList.addAll(flBean.data)
                        flSpinnerDialog.updatelist(flList)

                    } else {

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
                        flList.addAll(villageBean.data.bDOList)
                        ddList.addAll(villageBean.data.dDList)

                    }


                }
            }
            )
    }

}
