package com.dhanuka.indhan.Reports.View

import android.content.Intent
import android.os.Bundle
import android.view.View
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.NewTicketMVP.AreaManagerSpinnerDialog
import com.dhanuka.indhan.Activity.NewTicketMVP.BdoListSpinnerDialog
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.ProductBean
import com.dhanuka.indhan.Activity.NewTicketMVP.ProductSpinnerDialog
import com.dhanuka.indhan.Activity.NewTicketMVP.RegionalSpinnerDialog
import com.dhanuka.indhan.BDOPlanFilterActivity.MVP.BDOFilterFactory
import com.dhanuka.indhan.BDOPlanFilterActivity.MVP.BDOFilterPresenterImpl
import com.dhanuka.indhan.BDOPlanFilterActivity.Model.BDOFilterResponse
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.Reports.Model.ProductListResponse
import com.dhanuka.indhan.Reports.Reports_MVP.ProductListPresenterImpl
import com.dhanuka.indhan.Reports.Reports_MVP.ReportsFactory
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.OnSpinerItemClick
import com.dhanuka.indhan.util.SelectDateFragment
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.activity_filter.*

/**
 * Created by John on 5/27/2018.
 */
class FilterActivity : BaseActivity(), SelectDateFragment.myDialogListener,
    BDOFilterFactory.BDOFilterView, ReportsFactory.ProductHomeListener {

    private val product1RecordResponseModel = ArrayList<ProductBean>()

    lateinit var presenter: BDOFilterFactory.BDOFilterPresenter
    lateinit var presenter_productlist: ReportsFactory.ProductListPresenter
    lateinit var preference: Preference
    var userType: String = ""
    lateinit var regionalDialog: RegionalSpinnerDialog
    lateinit var productSpinnerDialog: ProductSpinnerDialog
    lateinit var bdoSpinnerDialog: BdoListSpinnerDialog
    var regionalList: ArrayList<BDOFilterResponse.RegionalManagerList> = ArrayList()
    lateinit var areaDialog: AreaManagerSpinnerDialog
    var areaList: ArrayList<BDOFilterResponse.AreaManagerList> = ArrayList()
    var areaAllList: ArrayList<BDOFilterResponse.AreaManagerList> = ArrayList()
    var bdoList: ArrayList<BDOFilterResponse.BDOList> = ArrayList()
    var bdoAllList: ArrayList<BDOFilterResponse.BDOList> = ArrayList()
    var startdate = ""
    var end = ""
    var fromDateStr = ""
    var toDateStr = ""
    var product = 0
    var product_name = ""
    var manager_id = 0
    var manager_name = ""
    var manager_type = ""
    var sr_manager_id = 0
    var sr_manager_name = ""
    var s1_id = 0
    var s1_name = ""

    /**
     * product response
     */
    override fun onSuccess(model: ProductListResponse) {
        if (model.data.productlist != null) {
            if (model.data.productlist.size > 0) {
                product1RecordResponseModel.clear()
                model.data.productlist.forEach {
                    val id = it.productId
                    val name = it.product
                    val bean = ProductBean()
                    bean.setpID(id)
                    bean.item = name
                    product1RecordResponseModel.add(bean)
                }
                productSpinnerDialog.updatelist(product1RecordResponseModel)
            }
        }
        if (userType.equals("ZONAL") || userType.contains("REGIONAL") || userType.contains("AREA")) {
            if (CheckConnectionStatus.isInternetAvailable(applicationContext)) {
                presenter.onRequest(preference.getString("token"))
            } else {
                makeToast(resources.getString(R.string.no_internet_connection))
            }
        }
    }

    override fun onEndDateSelected(endDate: String, value: Int) {
        if (Utility.getDateObj(endDate) != null) {
            if (Utility.getDateObj(endDate)!! < Utility.getDateObj(fromDateStr)) {
                makeToast("Please make sure End Date is greater than Start Date")
            }
        } else {
            toDateStr = endDate
            toDate.text = toDateStr
        }
    }

    override fun onStartDateSelected(startDate: String, value: Int) {
        fromDateStr = startDate
        fromDate.text = fromDateStr
    }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_filter)
        presenter = BDOFilterPresenterImpl(this)
        presenter_productlist = ProductListPresenterImpl(this)
        preference = Preference(this)
        fromDate.setOnClickListener(this)
        iCross.setOnClickListener(this)
        toDate.setOnClickListener(this)
        productName.setOnClickListener(this)
        reset.setOnClickListener(this)
        apply.setOnClickListener(this)
        s1.setOnClickListener(this)
        bdo.setOnClickListener(this)
        s2.setOnClickListener(this)
        userType = preference.getString("user_type")
        initFilter()
        initiateDialog()

    }

    private fun initFilter() {
        if (intent.hasExtra("from")) {

        } else {
            intent.getBundleExtra("bundle")?.let {
                fromDateStr = it.getString("startDate")!!
                toDateStr = it.getString("endDate")!!
                if (!fromDateStr.isNullOrEmpty())
                    fromDate.text = fromDateStr
                if (!toDateStr.isNullOrEmpty())
                    toDate.text = toDateStr
                product = it.getInt("product")
                product_name = it.getString("product_name")!!
                if (product != 0) {
                    productName.text = product_name
                }

                sr_manager_id = it.getInt("sr_manager_id")
                sr_manager_name = it.getString("sr_manager_name")!!
                manager_id = it.getInt("product")
                manager_type = it.getString("manager_level")!!
                manager_name = it.getString("manager_name")!!
            }

            if (!manager_type.isNullOrEmpty()) {
                if (manager_type.equals("AREA")) {
                    if (sr_manager_id != 0) {
                        layout_s2.visibility = View.VISIBLE
                        s2.text = sr_manager_name
                    }
                    layout_s1.visibility = View.VISIBLE
                    s1.text = manager_name
                } else if (manager_type.contains("REGIONAL")) {
                    layout_s2.visibility = View.VISIBLE
                    s2.text = manager_name
                }

                if (manager_type.equals("BDO")) {
                    if (sr_manager_id != 0) {
                        layout_s2.visibility = View.VISIBLE
                        s2.text = sr_manager_name
                    }
                    if (s1_id != 0) {
                        layout_s1.visibility = View.VISIBLE
                        s1.text = s1_name
                    }
                    bdo.visibility = View.VISIBLE
                    bdo.text = manager_name
                }
            }
        }
    }

    private fun initiateDialog() {
        regionalDialog = RegionalSpinnerDialog(this, regionalList, "Select Regional Manager")
        regionalDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                //  spinnerCrop.text = item
                s2.text = item
                manager_id = regionalList.get(position).regionalManagerID
                manager_name = regionalList.get(position).regionalNAME
                manager_type = "REGIONAL"
                var id = regionalList.get(position).regionalManagerID
                areaList.clear()
                if (areaAllList.size > 0) {
                    areaAllList.forEach {
                        if (it.regionalManagerID == id) {
                            areaList.add(it)
                        }
                    }
                    if (areaList.size > 0) {
                        areaDialog.updatelist(areaList)
                        layout_s1.visibility = View.VISIBLE
                    } else {
                        layout_s1.visibility = View.GONE
                    }
                } else {
                    layout_s1.visibility = View.GONE
                }
            }
        })
        areaDialog = AreaManagerSpinnerDialog(this, areaList, "Select Regional Manager")
        areaDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                s1.text = item
                if (manager_id != 0) {
                    sr_manager_id = manager_id
                    sr_manager_name = manager_name
                }
                manager_id = areaList.get(position).areaManagerID
                manager_name = areaList.get(position).areaNAME
                manager_type = "AREA"

                if (bdoAllList.size > 0) {
                    bdoList.clear()
                    bdoAllList.forEach {
                        if (it.areaManagerID == manager_id) {
                            bdoList.add(it)
                        }
                    }
                    if (bdoList.size > 0) {
                        bdoSpinnerDialog.updatelist(bdoList)
                        layout_bdo.visibility = View.VISIBLE
                    } else {
                        layout_bdo.visibility = View.GONE
                    }
                }
            }
        })
        productSpinnerDialog =
            ProductSpinnerDialog(this, product1RecordResponseModel, "Select or Search Product")
        productSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                productName.text = item
                product_name = item
                product = product1RecordResponseModel.get(position).getpID()!!
                //product1Id = product1RecordResponseModel[position].getpID()!!
                // product1Name = product1RecordResponseModel[position].item.toString()
            }
        })
        bdoSpinnerDialog = BdoListSpinnerDialog(this, bdoList, "Select or Search BDO")
        bdoSpinnerDialog.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {

                bdo.text = item
                if (!userType.contains("AREA")) {
                    if (manager_id != 0) {
                        s1_id = manager_id
                        s1_name = manager_name
                    }
                }
                manager_id = bdoList.get(position).bdoID
                manager_name = bdoList.get(position).bdoNAME
                manager_type = "BDO"
            }
        })
    }

    override fun onResume() {
        super.onResume()

        if (CheckConnectionStatus.isInternetAvailable(applicationContext)) {
            presenter_productlist.onRequestProductList(preference.getString("token"))
        } else {
            makeToast(resources.getString(R.string.no_internet_connection))
        }

    }

    override fun onClick(view: View) {
        when (view.id) {
            R.id.fromDate -> {
                fromDateStr = ""
                launchDateicker(2)
            }
            R.id.toDate -> {
                if (!fromDateStr.isNullOrEmpty()) {
                    toDateStr = ""
                    launchDateicker(3)
                } else
                    makeToast("Please Select Start Date")
            }
            R.id.iCross -> {
                finish()
            }
            R.id.productName -> {
                if (!productSpinnerDialog.isEnable)
                    productSpinnerDialog.showSpinerDialog()
            }
            R.id.s2 -> {
                if (!regionalDialog.isEnable)
                    regionalDialog.showSpinerDialog()
            }
            R.id.bdo -> {
                if (!bdoSpinnerDialog.isEnable)
                    bdoSpinnerDialog.showSpinerDialog()
            }
            R.id.s1 -> {
                if (!areaDialog.isEnable)
                    areaDialog.showSpinerDialog()
            }
            R.id.apply -> {
                proceedApply()
            }
            R.id.reset -> {
                resetEverything()
            }
        }
    }

    private fun resetEverything() {
        fromDateStr = ""
        toDateStr = ""
        product = 0
        product_name = ""
        manager_id = 0
        manager_name = ""
        manager_type = ""
        sr_manager_id = 0
        sr_manager_name = ""
        fromDate.text = "Start Date"
        toDate.text = "End Date"
        productName.text = "Select Product"
        if (userType.equals("ZONAL")) {
            layout_s2.visibility = View.VISIBLE
            s2.text = "Select S2"
            layout_s1.visibility = View.GONE
            s1.text = "Select S1"
        }
        if (userType.equals("REGIONAL")) {
            layout_s1.visibility = View.VISIBLE
            s1.text = "Select S1"
        }

    }

    private fun proceedApply() {
        val intent = Intent()
        val b = Bundle()
        b.putString("startDate", fromDateStr)
        b.putString("endDate", toDateStr)
        b.putInt("product", product)
        b.putString("product_name", product_name)
        b.putInt("manager_id", manager_id)
        b.putString("manager_level", manager_type)
        b.putString("manager_name", manager_name)
        b.putInt("sr_manager_id", sr_manager_id)
        b.putString("sr_manager_name", sr_manager_name)
        b.putInt("s1_id", s1_id)
        b.putString("s1_name", s1_name)
        intent.putExtra("bundle", b)
        setResult(101, intent)
        finish()
    }

    private fun launchDateicker(i: Int) {
        val bundle = Bundle()
        bundle.putInt("key", i)
        val newFragment = SelectDateFragment()
        newFragment.setArguments(bundle)
        newFragment.show(this@FilterActivity.supportFragmentManager, "DatePicker")
    }

    /**
     * list response
     */
    override fun onSuccess(model: BDOFilterResponse) {
        if (userType.equals("ZONAL")) {
            layout_s2.visibility = View.VISIBLE

            regionalList.clear()

            if (model.data.regionalManagerList != null) {
                regionalList.clear()
                if (model.data.regionalManagerList.size > 0) {
                    regionalList.addAll(model.data.regionalManagerList)
                }
                regionalDialog.updatelist(regionalList)
            }
            if (model.data.areaManagerList != null) {
                areaAllList.clear()
                if (model.data.areaManagerList.size > 0) {
                    areaAllList.addAll(model.data.areaManagerList)
                }
            }
            bdoAllList.clear()
            if (model.data.bdoList != null) {
                bdoAllList.addAll(model.data.bdoList)
            }

        } else if (userType.equals("REGIONAL")) {
            layout_s1.visibility = View.VISIBLE

            if (model.data.areaManagerList != null) {
                areaAllList.clear()
                if (model.data.areaManagerList.size > 0) {
                    areaList.addAll(model.data.areaManagerList)
                }
                areaDialog.updatelist(areaList)
            }
            bdoAllList.clear()
            if (model.data.bdoList != null) {
                bdoAllList.addAll(model.data.bdoList)
            }
        } else if (userType.contains("AREA")) {
            layout_bdo.visibility = View.VISIBLE
            if (model.data.bdoList != null) {
                bdoAllList.clear()
                if (model.data.bdoList.size > 0) {
                    bdoAllList.addAll(model.data.bdoList)
                }
                bdoSpinnerDialog.updatelist(bdoAllList)
                bdoList.addAll(bdoAllList)
            }
        }

    }

    override fun onFailure(msg: String) {

    }

}
