package com.dhanuka.indhan.PlanATP.View

import android.app.Dialog
import android.graphics.Color
import android.graphics.drawable.ColorDrawable
import android.os.Bundle
import android.view.View
import android.view.Window
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.DataBase.TableDataModel.MySyllabusDBModel
import com.dhanuka.indhan.DataBase.TableDataModel.PlanAtpDBModel
import com.dhanuka.indhan.DataSyncServices.DataSyncManager
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInterfaceFactory
import com.dhanuka.indhan.Fonts.Medium
import com.dhanuka.indhan.MySyllabusBDO.View.MySyllabusActivity
import com.dhanuka.indhan.PlanATP.Model.TempModel
import com.dhanuka.indhan.PlanATP.presenter.ATPPresenter
import com.dhanuka.indhan.PlanATP.presenter.ATPPresenterImpl
import com.dhanuka.indhan.PlanSyllabusForBdo.Adapter.AtpAdapter
import com.dhanuka.indhan.PlanSyllabusForBdo.Adapter.AtpSummaryAdapter
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.activity_atp.*

/**
 * Created by John on 5/14/2018.
 */
class ATPActivity : BaseActivity(), SyncServiceInterfaceFactory.FailureListener, ATPPresenter.AtpUI,
    SwipeRefreshLayout.OnRefreshListener, AtpSummaryAdapter.onClickListener {


    override fun onInvalidAttempt() {
        hideProgress()
    }

    override fun onFailure(msg: String) {
        updatelist()
        hideProgress()
        showsuccessdialog()
    }

    override fun onDBSyncSuccess() {
        updatelist()
        hideProgress()
        showsuccessdialog()
    }

    lateinit var preference: Preference
    lateinit var adapter: AtpAdapter
    lateinit var adapterSummary: AtpSummaryAdapter
    var atpList: ArrayList<PlanAtpDBModel> = ArrayList()
    var test: ArrayList<PlanAtpDBModel> = ArrayList()
    lateinit var presenter: ATPPresenter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_atp)
        preference = Preference(this)
        pd_name.text = "ATP"
        submit.text = "Share to Supervisor"
        presenter = ATPPresenterImpl(this)
        iCross.setOnClickListener(this)
        swipe_layout.setOnRefreshListener(this)
        submit.setOnClickListener(this)
        test.addAll(atpList)
        test.sort()
        rcViewProdDes.layoutManager =
            LinearLayoutManager(this)
        adapter = AtpAdapter(test, this)
        rcViewProdDes.adapter = adapter
        rcview_summary.layoutManager =
            LinearLayoutManager(this)
        rcview_summary.setOnClickListener(this)
        adapterSummary = AtpSummaryAdapter(model_temp, this)
        adapterSummary.setClickListener(this)
        rcview_summary.adapter = adapterSummary
        updatelist()
    }

    private fun updatelist() {
        //all list
        val atplist = DatabaseManager.getAllPlanATPList(this, 0)
        //  var listtwo = DatabaseManager.getAllPlanATPList(this, 1)

        atpList.clear()
        atpList.addAll(atplist)
        // atpList.addAll(listtwo)
        test.clear()
        test.addAll(atpList)
        test.sort()
        //if (test.size > 0) {
        if (test.size > 0) {
            detailbanner.visibility = View.VISIBLE
        } else detailbanner.visibility = View.GONE
        rcViewProdDes.visibility = View.VISIBLE
        tv_nodata.visibility = View.GONE

        summary_banner.visibility = View.VISIBLE
        updateAnotherAdapter()

    }

    var model_temp = ArrayList<TempModel>()

    private fun updateAnotherAdapter() {
        //model_temp is adapter of the table
        model_temp.clear()
        test.forEach {
            if (!checkWhetherPresentofNOt(it)) {
                var model = TempModel()
                model.cropId = it.crop_id
                model.crop_name = it.crop
                model.prodId = it.product_id
                model.prod_name = it.product
                model.no_of_days = 1
                model.isUploaded = it.isUploaded
                model.month = Utility.getMontfromDate(it.date)
                var months = Utility.getMonth(model.month - 1)
                var syllabus = DatabaseManager.getAllMySyllabusListonThebasisofCropnProduct(
                    this,
                    it.product_id,
                    it.crop_id,
                    model.month
                )
                if (syllabus != null) {
                    model.syllabusDays = syllabus.no_of_days
                    model.allready_uploaded = syllabus.atp
                } else {
                    model.allready_uploaded = 0
                    model.syllabusDays = 0
                }
                model_temp.add(model)
            }
        }
        updateListWithAllSyllabus()
        model_temp.sort()
        adapterSummary.notifyDataSetChanged()
        adapter.notifyDataSetChanged()
    }

    private fun updateListWithAllSyllabus() {
        //update the list, who are not planned yet.
        val bean = DatabaseManager.getAllMySyllabusList(this)
        bean.forEach {
            if (!isAllreadyPresentInModel(it)) {
                var model = TempModel()
                model.cropId = it.crop_id
                model.crop_name = it.crop_name
                model.prodId = it.product_id
                model.prod_name = it.product_name
                model.no_of_days = 0
                model.isUploaded = 1
                model.month = it.month
                model.allready_uploaded = it.atp
                model.syllabusDays = it.no_of_days
                model_temp.add(model)
            }
        }
    }

    private fun isAllreadyPresentInModel(model: MySyllabusDBModel?): Boolean {
        model_temp.forEach {
            if ((it.cropId == model!!.crop_id) && (it.prodId == model!!.product_id) && (it.month == model!!.month)) {
                return true
            }
        }
        return false
    }


    override fun onSelected() {
        launchIntent(MySyllabusActivity::class.java, true)
    }

    override fun onRefresh() {
        swipe_layout.setRefreshing(false)
    }

    fun checkWhetherPresentofNOt(model: PlanAtpDBModel): Boolean {
        model_temp.forEach {
            if (model.crop_id == it.cropId && model.product_id == it.prodId && Utility.getMontfromDate(
                    model.date
                ) == it.month && it.isUploaded == model.isUploaded
            ) {
                it.no_of_days += 1
                if (it.isUploaded != 0) {
                    it.isUploaded = model.isUploaded
                }
                return true
            }
        }
        return false
    }

    override fun onClick(view: View) {
        when (view.id) {
            R.id.iCross -> {
                finish()
            }
            R.id.submit -> {
                checkCantUpLoadMonths()
                checkStatus()
                if (list_to_be_uploaded.size > 0)
                    submitAPI()
                else showdialog()
            }
        }
    }

    var cantUpLoadMonthsHashSet = HashSet<Int>()
    private fun checkCantUpLoadMonths() {
        model_temp.forEach {
            if (((it.no_of_days + it.allready_uploaded) >= it.syllabusDays)) {

            } else {
                it.isUploaded = 0
            }
            if (it.isUploaded == 0) {
                if (((it.no_of_days + it.allready_uploaded) >= it.syllabusDays)) {

                } else
                    cantUpLoadMonthsHashSet.add(it.month)
            }
        }
    }

    var list_to_be_uploaded = ArrayList<TempModel>()
    var not_uploaded_month = HashSet<Int>()

    fun checkStatus() {
        list_to_be_uploaded.clear()
        var listuploaded = ArrayList<TempModel>()
        model_temp.forEach {
            if (it.isUploaded == 0) {
                if (((it.no_of_days + it.allready_uploaded) >= it.syllabusDays))
                    listuploaded.add(it)
                else
                    not_uploaded_month.add(it.month)
            }
        }
        listuploaded.forEach {
            if (!checkmonth(it.month)) {
                list_to_be_uploaded.add(it)
            }
        }
    }

    private fun checkmonth(month: Int): Boolean {
        not_uploaded_month.forEach {
            if (it == month) {
                return true
            }
        }
        return false
    }

    private fun submitAPI() {
        if (test.size > 0) {

            val model = getModelTobeUploaded();
            if (CheckConnectionStatus.isInternetAvailable(applicationContext)) {
                presenter.onRequest(preference.getString("token"), model)
            } else {
                makeToast(resources.getString(R.string.no_internet_connection))
            }
        } else {

            makeToast("No Planned ATP")
        }

    }

    private fun getModelTobeUploaded(): ArrayList<PlanAtpDBModel> {
        val model1 = DatabaseManager.getAllPlanATPList(this, 0)
        val model_ret = ArrayList<PlanAtpDBModel>()
        list_to_be_uploaded.forEach {
            for (i in 0 until model1.size) {
                var model = model1[i]
                if (model.crop_id == it.cropId && model.product_id == it.prodId && Utility.getMontfromDate(
                        model.date
                    ) == it.month
                ) {
                    model_ret.add(model)
                }
            }
        }
        return model_ret
    }

    override fun onSuccess() {
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            val manager = DataSyncManager(this, this)
            manager.getMySyllabus(0)
        }
    }

    private fun showdialog() {
        val dialog = Dialog(this)
        dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_cancel_number)
        dialog.setCanceledOnTouchOutside(false)
        dialog.setCancelable(false)
        val ticketNumber = dialog.findViewById<View>(R.id.tvMeTicketNumber) as Medium
        val msg = dialog.findViewById<View>(R.id.msg) as Medium
        val tic_banner = dialog.findViewById<View>(R.id.tic_banner) as Medium
        ticketNumber.visibility = View.GONE
        tic_banner.visibility = View.GONE
        msg.text = "Please plan your ATP according to the Syllabus"
        val tvOkay = dialog.findViewById<View>(R.id.tvMeOkay) as Medium
        tvOkay.setOnClickListener {
            dialog.dismiss()
        }
        dialog.show()
    }

    private fun showsuccessdialog() {
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
        msg.text = "Successfully Shared"
        val tvOkay = dialog.findViewById<View>(R.id.tvMeOkay) as Medium
        tvOkay.setOnClickListener {
            dialog.dismiss()
        }
        dialog.show()
    }

    override fun onFailureATPshare(msg: String?) {
        hideProgress()
        makeToast(msg)
    }
}
