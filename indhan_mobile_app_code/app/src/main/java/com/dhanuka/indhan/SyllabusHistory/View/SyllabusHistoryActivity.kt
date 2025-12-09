package com.dhanuka.indhan.PlanSyllabusForBdo.View

import android.app.Dialog
import android.graphics.Color
import android.graphics.drawable.ColorDrawable
import android.os.Bundle
import androidx.recyclerview.widget.LinearLayoutManager
import android.view.View
import android.view.Window
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.ProductBean
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.DataBase.TableDataModel.ProductCropResponse
import com.dhanuka.indhan.DataBase.TableDataModel.UserTeamModel
import com.dhanuka.indhan.DataSyncServices.Model.PlanSyllabusModel
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInterfaceFactory
import com.dhanuka.indhan.Fonts.Medium
import com.dhanuka.indhan.PlanSyllabusForBdo.Adapter.HistoryBdoFilteredAdapter
import com.dhanuka.indhan.PlanSyllabusForBdo.Adapter.HistoryProductFilteredAdapter
import com.dhanuka.indhan.PlanSyllabusForBdo.Dialog.NumberSpinner
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.SyllabusHistory.Model.BDListForSyllabusModel
import com.dhanuka.indhan.SyllabusHistory.Model.ProdListForSyllabusHistory
import com.dhanuka.indhan.SyllabusHistory.Presenter.SyllabusHistoryPresenterImpl
import com.dhanuka.indhan.SyllabusHistory.View.SyllabusBdoDescriptionActivity
import com.dhanuka.indhan.SyllabusHistory.View.SyllabusProductDescriptionActivity
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.OnSpinerItemClick
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.activity_syllabus_history.*

/**
 * Created by John on 5/14/2018.
 */
class SyllabusHistoryActivity : BaseActivity(), SyncServiceInterfaceFactory.SyllabusHistoryView,
    HistoryProductFilteredAdapter.onClickProductListener,
    SyncServiceInterfaceFactory.FailureListener, HistoryBdoFilteredAdapter.onClickBdoListener {
    override fun onFailure(msg: String) {
    }

    override fun onDBSyncSuccess() {
    }

    lateinit var historyBDOAdapter: HistoryBdoFilteredAdapter
    lateinit var historyProductAdapter: HistoryProductFilteredAdapter
    lateinit var preference: Preference
    var bd_list_model: ArrayList<BDListForSyllabusModel> = ArrayList()
    var product_list_model: ArrayList<ProdListForSyllabusHistory> = ArrayList()
    var month: String = ""
    private lateinit var monthSpinner: NumberSpinner
    var isBdoActive: Boolean = true
    lateinit var presenter: SyncServiceInterfaceFactory.SyllabusHistoryPresenter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_syllabus_history)
        presenter = SyllabusHistoryPresenterImpl(this)
        historyBDOAdapter = HistoryBdoFilteredAdapter(bd_list_model, this)
        historyProductAdapter = HistoryProductFilteredAdapter(product_list_model, this)
        historyProductAdapter.setOnclickListener(this)
        historyBDOAdapter.setOnclickListener(this)
        preference = Preference(this)
        rcViewSyllabusHistory.layoutManager =
            LinearLayoutManager(this)
        filterByProductBtn.setOnClickListener(this)
        filterByBDOBtn.setOnClickListener(this)
        var monthlist: ArrayList<String> = Utility.monthList
        spinnersyllabusMonth.text = monthlist.get(0)
        month = monthlist.get(0)
        monthSpinner = NumberSpinner(this, monthlist, "Select Month")
        monthSpinner.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                month = item
                spinnersyllabusMonth.text = item
                initiateListChange()
            }
        })
        submit_api.setOnClickListener(this)
        iCross.setOnClickListener(this)
        spinnersyllabusMonth.setOnClickListener(this)
    }

    private fun initiateListChange() {
        if (isBdoActive) filterByBDO() else filterByProduct()
    }

    override fun onClick(view: View) {
        when (view.id) {
            R.id.filterByProductBtn -> {

                if (month.isNullOrEmpty())
                    makeToast("Please Select the Month")
                else
                    filterByProduct()
            }
            R.id.filterByBDOBtn -> {
                if (month.isNullOrEmpty())
                    makeToast("Please Select the Month")
                else
                    filterByBDO()
            }
            R.id.spinnersyllabusMonth -> {
                monthSpinner.showSpinerDialog()
            }
            R.id.submit_api -> {
                submitAPI()
            }
            R.id.iCross -> {
                finish()
            }
        }
    }

    private fun updateBtn(b: Boolean) {
        if (b) {
            filterByProductBtn.setImageResource(R.drawable.product_wise_green)
            filterByBDOBtn.setImageResource(R.drawable.bdo_wise_grey)
        } else {
            filterByProductBtn.setImageResource(R.drawable.product_wise_grey)
            filterByBDOBtn.setImageResource(R.drawable.bdo_wise_green)
        }
    }

    private fun submitAPI() {
        if (checkAccessPermission()) {
            if (CheckConnectionStatus.isInternetAvailable(this)) {
                val syllabus = DatabaseManager.getAllNonSyncedSyllabus(this)
                if (syllabus.size > 0)
                    presenter.onRequest(preference.getString("token"), syllabus)
                else {
                    makeToast("No new Syllabus Planned")
                }
            } else {
                makeToast(resources.getString(R.string.no_internet_connection))
            }
        }
    }

    private fun checkAccessPermission(): Boolean {
        return true
    }

    private fun filterByBDO() {
        updateBtn(false)
        isBdoActive = true
        bd_list_model.clear()
        var bdo_list: ArrayList<UserTeamModel> = DatabaseManager.getAllUserTeamDataList(this)
        bdo_list.forEach {
            var bdo_id: Int = it.id
            var noOdDays: Int = DatabaseManager.getNoOfDaysOfBDO(this, bdo_id, month, 0)
            var submittednoOdDays: Int = DatabaseManager.getNoOfDaysOfBDO(this, bdo_id, month, 1)
            var bdo_name: String = it.username
            var model = BDListForSyllabusModel()
            model.noOdDays = noOdDays
            model.month = month
            model.bdo_name = bdo_name
            model.bdo_id = bdo_id
            model.submitted_noOfDays = submittednoOdDays
            if (noOdDays > 0 || submittednoOdDays > 0) {
                bd_list_model.add(model)
            }
        }
        if (bd_list_model.size > 0) {
            nodata.visibility = View.GONE
            rcViewSyllabusHistory.visibility = View.VISIBLE
        } else {
            nodata.visibility = View.VISIBLE
            rcViewSyllabusHistory.visibility = View.GONE
        }
        rcViewSyllabusHistory.adapter = historyBDOAdapter
        historyBDOAdapter.notifyDataSetChanged()
    }

    private var prodCropResponseModel: ArrayList<ProductCropResponse.ProductCropList> = ArrayList();

    private fun updateProductList(): ArrayList<ProductBean> {
        var product_list: ArrayList<ProductBean> = ArrayList()
        prodCropResponseModel = DatabaseManager.getProductCropDetails(this)
        if (prodCropResponseModel != null) {
            prodCropResponseModel.forEach {
                var bean = ProductBean()
                bean.setpID(it.productId.toInt())
                bean.item = it.product
                if (!checkValue(bean.getpID()!!, product_list))
                    product_list.add(bean)
            }
        }
        return product_list
    }

    private fun checkValue(id: Int, product_list: ArrayList<ProductBean>): Boolean {
        product_list.forEach {
            if (id == it.getpID()) {
                return true
            }
        }
        return false
    }

    private fun filterByProduct() {
        updateBtn(true)
        isBdoActive = false
        product_list_model.clear()
        val product_list: ArrayList<ProductBean> = updateProductList()
        product_list.forEach {
            val productId: Int = it.getpID()!!
            val productName = it.item
            val map = DatabaseManager.getNoOfBDOandNoOfBDOFromSyllabus(this, productId, month, 0)
            val noOfDays = map.get("no_of_days")
            val noOdBDO = map.get("bdo_no")
            val map2 = DatabaseManager.getNoOfBDOandNoOfBDOFromSyllabus(this, productId, month, 1)
            val subnoOfDays = map2.get("no_of_days")
            val subnoOdBDO = map2.get("bdo_no")
            val model = ProdListForSyllabusHistory()
            model.month = month
            model.noOfBDO = noOdBDO!!
            model.noOfDays = noOfDays!!
            model.productId = productId
            model.productName = productName
            model.submittedNo_ifDays = subnoOfDays!!
            model.submittednoOfBDO = subnoOdBDO!!
            if ((noOfDays > 0 && noOdBDO > 0) || (subnoOfDays > 0 && subnoOdBDO > 0)) {
                product_list_model.add(model)
            }
        }
        if (product_list_model.size > 0) {
            nodata.visibility = View.GONE
            rcViewSyllabusHistory.visibility = View.VISIBLE
        } else {
            nodata.visibility = View.VISIBLE
            rcViewSyllabusHistory.visibility = View.GONE
        }
        rcViewSyllabusHistory.adapter = historyProductAdapter
        historyProductAdapter.notifyDataSetChanged()
    }

    override fun onProductSelected(product: ProdListForSyllabusHistory) {
        if (product.noOfDays > 0) {
            var b = Bundle()
            b.putInt("product_id", product.productId)
            b.putString("product_name", product.productName)
            b.putString("month", month)
            launchIntent(SyllabusProductDescriptionActivity::class.java, b, false)
        } else {
            makeToast("No New Syllabus planned for this product")
        }
    }

    override fun onBdoSelected(bdo: BDListForSyllabusModel) {
        if (bdo.noOdDays > 0) {
            var b = Bundle()
            b.putInt("bdo_id", bdo.bdo_id)
            b.putString("bdo_name", bdo.bdo_name)
            b.putString("month", month)
            launchIntent(SyllabusBdoDescriptionActivity::class.java, b, false)
        } else {
            makeToast("No New Syllabus planned for this BDO")
        }
    }

    override fun onResume() {
        super.onResume()
        if (!month.isNullOrEmpty()) {
            initiateListChange()
        }
    }

    override fun onPlanSyllabusSuccess(homeBean: PlanSyllabusModel) {
        initiateListChange()
        showdialog()
    }

    override fun onInvalidAttempt() {
    }

    private fun showdialog() {
        val dialog = Dialog(this)
        dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_ticket_number)
        dialog.setCanceledOnTouchOutside(false)
        dialog.setCancelable(false)
        val ticketNumber = dialog.findViewById<View>(R.id.tvMeTicketNumber) as Medium
        val msg = dialog.findViewById<View>(R.id.msg) as Medium
        val tic_banner = dialog.findViewById<View>(R.id.tic_banner) as Medium
        ticketNumber.visibility = View.GONE
        tic_banner.visibility = View.GONE
        msg.text = "Your Syllabus Plan is successfully shared with BDO, S2, S3"
        val tvOkay = dialog.findViewById<View>(R.id.tvMeOkay) as Medium
        tvOkay.setOnClickListener {
            dialog.dismiss()
            finish()
        }
        dialog.show()
    }

}
