package com.dhanuka.indhan.DemoRequestEvaluated.fragments

import android.os.Bundle
import android.text.TextUtils
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.fragment.app.Fragment
import com.dhanuka.indhan.Activity.NewTicketMVP.CropSpinnerDialog
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.CropBean
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.ProductBean
import com.dhanuka.indhan.Activity.NewTicketMVP.ProductSpinnerDialog
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.DataBase.TableDataModel.ProductCropResponse
import com.dhanuka.indhan.DemoRequestEvaluated.activities.DemoUtilizationSummaryActivity
import com.dhanuka.indhan.DemoRequestEvaluated.adapters.UtilizationSummaryAdapter
import com.dhanuka.indhan.DemoRequestEvaluated.models.ASMData
import com.dhanuka.indhan.DemoRequestEvaluated.models.UtilizationSummary
import com.dhanuka.indhan.DemoRequestEvaluated.models.UtilizationSummaryData
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.OnSpinerItemClick
import kotlinx.android.synthetic.main.fragment_utilization_summary.*
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*
import kotlin.collections.ArrayList
import kotlin.collections.set

class UtilizationSummaryList : Fragment(), View.OnClickListener,
    UtilizationSummaryAdapter.RequestedMaterialClick {
    private var adapter: UtilizationSummaryAdapter? = null
    private var asmName: String = ""
    private lateinit var preference: Preference
    private lateinit var mContext: DemoUtilizationSummaryActivity
    private var userType: String = ""
    private var rsmListSpinner: NumberSpinner? = null
    private var arrayListAsm: ArrayList<ASMData> = ArrayList()
    private var stringListAsm: ArrayList<String> = ArrayList()
    private var prodCropResponseModel: java.util.ArrayList<ProductCropResponse.ProductCropList> =
        java.util.ArrayList()
    private var productList: java.util.ArrayList<ProductBean> = java.util.ArrayList()
    private var cropList: java.util.ArrayList<CropBean> = java.util.ArrayList()
    private var productSpinner: ProductSpinnerDialog? = null
    private var cropSpinner: CropSpinnerDialog? = null
    private var product_id = 0
    private var product = ""
    private var crop_id = 0
    private var crop = ""

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        mContext = requireContext() as DemoUtilizationSummaryActivity
        preference = Preference(mContext)
        return inflater.inflate(R.layout.fragment_utilization_summary, container, false)
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        userType = mContext.userType
        txtAsmFilter.setOnClickListener(this)
        txtFilterProduct.setOnClickListener(this)
        txtFilterCrop.setOnClickListener(this)
        btnReset.setOnClickListener(this)
        if (userType.equals("area", ignoreCase = true)
            || userType.equals("sales_area", ignoreCase = true)
        ) {
            txtAsmFilter.visibility = View.VISIBLE
        } else {
            txtAsmFilter.visibility = View.GONE
        }
    }


    private fun updateCropList() {
        prodCropResponseModel = DatabaseManager.getProductCropDetails(mContext)
        val prpdCropListNew = DatabaseManager.getProductCropDetailProductNew(mContext)
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

    override fun onActivityCreated(savedInstanceState: Bundle?) {
        super.onActivityCreated(savedInstanceState)
        callApiGetSummaryData("")
    }

    private fun setAsmData() {
        arrayListAsm = mContext.arrayListAsm
        updateCropList()
        stringListAsm.clear()
        arrayListAsm.forEach() {
            stringListAsm.add(it.name)
        }
        productSpinner = ProductSpinnerDialog(mContext, productList, "Select Product")
        productSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                txtFilterProduct.text = item
                product = item
                product_id = productList[position].getpID()!!
                if (!userType.equals("sales_regional", ignoreCase = true)) {
                    callApiGetSummaryData("")
                }
            }
        })

        cropSpinner = CropSpinnerDialog(mContext, cropList, "Select Crop")
        cropSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                txtFilterCrop.text = item
                crop = item
                crop_id = cropList[position].getcID() ?: 0
                updateProductList()
            }
        })
        rsmListSpinner = NumberSpinner(mContext, stringListAsm, "Select ASM")
        rsmListSpinner?.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                txtAsmFilter.text = item
                asmName = item
                val asmEcn = arrayListAsm[position].ECN
                callApiGetSummaryData(asmEcn)
            }
        })
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

    private fun callApiGetSummaryData(asmEcn: String) {
        mContext.showProgress()
        val query = HashMap<String, Any>()
        query["apiToken"] = preference.getString("token")
        query["updated_date"] = "0"
        query["asm_ecn"] = asmEcn
        query["crop_id"] = crop_id
        query["product_id"] = product_id
        if (CheckConnectionStatus.isInternetAvailable(requireContext())) {
            RetrofitClient.mvpClient
                .create(ApiServiceRequest::class.java).getUtilizationSummary(query)
                .subscribeOn(Schedulers.newThread())
                .observeOn(AndroidSchedulers.mainThread())
                .subscribe(object : Subscriber<UtilizationSummary>() {
                    override fun onCompleted() {
                        mContext.hideProgress()
                    }

                    override fun onError(e: Throwable) {
                        mContext.hideProgress()
                    }

                    override fun onNext(atpBean: UtilizationSummary) {
                        mContext.hideProgress()
                        if (atpBean.status_code == 200) {
                            setSummaryData(atpBean.data)
                        }
                        setAsmData()
                    }
                })
        } else {
            mContext.makeToast("Internet connection not available.")
        }
    }

    private fun setSummaryData(data: ArrayList<UtilizationSummaryData>) {
        adapter = UtilizationSummaryAdapter(data, mContext)
        recyclerUtilizationSummary.adapter = adapter
        adapter?.setClickListener(this)

        if (data.size > 0) {
            recyclerUtilizationSummary.visibility = View.VISIBLE
            tvNoData.visibility = View.GONE
        } else {
            recyclerUtilizationSummary.visibility = View.GONE
            tvNoData.visibility = View.VISIBLE
        }
    }

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.txtAsmFilter -> {
                if (!TextUtils.isEmpty(product)) {
                    rsmListSpinner?.let {
                        if (!it.isEnable)
                            it.showSpinerDialog()
                    }
                } else {
                    mContext.makeToast("Please select product first.")
                }
            }
            R.id.txtFilterCrop -> {
                cropSpinner?.let {
                    if (!it.isEnable)
                        it.showSpinerDialog()
                }
            }
            R.id.btnReset -> {
                asmName = ""
                product_id = 0
                product = ""
                crop_id = 0
                crop = ""
                txtFilterProduct.text = product
                txtFilterCrop.text = crop
                txtAsmFilter.text = asmName
                callApiGetSummaryData("")
            }
            R.id.txtFilterProduct -> {
                if (!TextUtils.isEmpty(crop)) {
                    productSpinner?.let {
                        if (!it.isEnable)
                            it.showSpinerDialog()
                    }
                } else {
                    mContext.makeToast("Please select crop first.")
                }

            }
        }
    }

    override fun onClickRequestedMaterial(data: UtilizationSummaryData) {

    }
}