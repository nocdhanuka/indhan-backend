package com.dhanuka.indhan.PlanATP.View

import android.app.Dialog
import android.content.Intent
import android.graphics.Color
import android.graphics.drawable.ColorDrawable
import android.os.Bundle
import android.view.View
import android.view.Window
import androidx.recyclerview.widget.LinearLayoutManager
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.NewTicketMVP.BDOSpinnerDialog
import com.dhanuka.indhan.Fonts.Medium
import com.dhanuka.indhan.NewTicketActivity.NewTicketActivity
import com.dhanuka.indhan.PlanSyllabusForBdo.Adapter.PlanParticipationAdapter
import com.dhanuka.indhan.PlanSyllabusForBdo.Adapter.SelfParticipationAdapter
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.SelfParticipationActivity.Model.*
import com.dhanuka.indhan.SelfParticipationActivity.Model.SelfParticipationResponse.BdoList
import com.dhanuka.indhan.SelfParticipationActivity.SP_MVP.SelfParticipationFactory
import com.dhanuka.indhan.SelfParticipationActivity.SP_MVP.SelfParticipationPresenterImpl
import com.dhanuka.indhan.SelfParticipationActivity.View.SPFilterActivity
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.OnSpinerItemClick
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.activity_self_participation.*
import java.util.*

/**
 * Created by John on 5/26/2018.
 */
class SelfParticipationActivity : BaseActivity(),
    SelfParticipationAdapter.SelfParticipationClickListener,
    PlanParticipationAdapter.PlanParticipationAdapterClickListener,
    SelfParticipationFactory.HomeListener {

    override fun onSPDetailSuccess(model: SPDetailModel) {
        if (model.data.size > 0) {
            val b = Bundle()
            b.putSerializable("ticketDetails", model.data[0])
            b.putBoolean("isactive", true)
            launchIntent(SelfParticipationDetailsActivity::class.java, b, false)
        } else {
            makeToast("No Data")
        }
    }

    lateinit var preference: Preference
    lateinit var adapter: SelfParticipationAdapter
    lateinit var planAdapter: PlanParticipationAdapter
    lateinit var allreadyAdapter: PlanParticipationAdapter
    var atpList: ArrayList<TicketList> = ArrayList()
    var bdoList: ArrayList<BdoList> = ArrayList()
    var tobePlanList: ArrayList<AtpList> = ArrayList()
    var plannedList: ArrayList<AtpList> = ArrayList()
    var MYPLAN = "myplan"
    var ACTIVEPLAN = "activeplan"
    var PLAN = "plan"
    var HISTORY = "history"
    var CURR_SCR = ACTIVEPLAN
    lateinit var presenter: SelfParticipationFactory.SelfParticipationPresenter
    lateinit var spinner: BDOSpinnerDialog
    var userType = ""
    var manager_id = 0
    var manager_name = ""
    var manager_level = ""
    var sr_manager_id = 0
    var sr_manager_name = ""
    var isPending = 0

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_self_participation)
        preference = Preference(this)
        iCross.setOnClickListener(this)
        spinnerbospinner.setOnClickListener(this)
        plan.setOnClickListener(this)
        new_participation.setOnClickListener(this)
        history.setOnClickListener(this)
        filter.setOnClickListener(this)
        myAtp.setOnClickListener(this)
        presenter = SelfParticipationPresenterImpl(this)
        adapter = SelfParticipationAdapter(atpList)
        adapter.setClickListener(this)
        planAdapter = PlanParticipationAdapter(tobePlanList, this)
        allreadyAdapter = PlanParticipationAdapter(plannedList, this)
        planAdapter.setClickListener(this)
        allreadyAdapter.setClickListener(this)
        spinner = BDOSpinnerDialog(this, bdoList, "Select BDO")
        spinner.bindOnSpinerListener(object : OnSpinerItemClick {
            override fun onClick(item: String, position: Int) {
                spinnerbospinner.text = item
                adapter.filter(item)
            }
        })
        rcViewProdDes.layoutManager =
            LinearLayoutManager(this)
        userType = preference.getString("user_type")
        if (userType == "ZONAL" || userType.contains("REGIONAL"))
            filter.visibility = View.VISIBLE
        else
            filter.visibility = View.GONE
    }

    override fun onSelected(model: TicketList) {
        if (isPending == 0) {
            val b = Bundle()
            b.putSerializable("ticketDetails", model)

            if (CURR_SCR.equals(ACTIVEPLAN)) {
                b.putBoolean("isactive", true)
                presenter.onGetDetails(preference.getString("token"), model.tid.toString())
            } else {
                b.putBoolean("isactive", false)
                launchIntent(SelfParticipationDetailsActivity::class.java, b, false)

            }
        } else {
            val b = Bundle()
            b.putSerializable("ticketDetails", model)
            if (CURR_SCR.equals(HISTORY)) {
                b.putBoolean("isactive", false)
                launchIntent(SelfParticipationDetailsActivity::class.java, b, false)
            } else {
                showdialog()
            }
        }
    }

    private fun showdialog() {
        val dialog = Dialog(this)
        dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_cancel_number)
        dialog.setCanceledOnTouchOutside(false)
        dialog.setCancelable(true)
        val ticketNumber = dialog.findViewById<View>(R.id.tvMeTicketNumber) as Medium
        val msg = dialog.findViewById<View>(R.id.msg) as Medium
        val tic_banner = dialog.findViewById<View>(R.id.tic_banner) as Medium
        ticketNumber.visibility = View.GONE
        tic_banner.visibility = View.GONE
        msg.text = "You have not Participated in some Tickets, Please provide the reason"
        val tvOkay = dialog.findViewById<View>(R.id.tvMeOkay) as Medium
        tvOkay.setOnClickListener {
            dialog.dismiss()
            if (tobePlanList.size > 0) {
                val b = Bundle()
                val list = ArrayList<AtpList>()
                list.clear()
                tobePlanList.forEach {
                    if (it.planAtpStatus == 1 && it.valid_status < 0) {
                        list.add(it)
                    }
                }
                b.putSerializable("atpDetails", list)
                b.putInt("isPending", isPending)
                launchIntent(SelfParticipationReasonActivity::class.java, b, false)
            } else {
                makeToast("No ATP List")
            }
        }
        dialog.show()
    }

    override fun onFailure(mes: String) {
        hideProgress()
        if (!mes.equals("Failure"))
            makeToast(mes)
    }

    override fun onSuccess(model: SelfParticipationResponse) {
        hideProgress()

        if (model.data.ticketList != null) {
            if (model.data.atpList != null) {
                model.data.atpList.forEach {
                    it.date = Utility.getDateFromsfDateTimeFormat(it.datetimeFormat)
                }
            }
            model.data.ticketList.forEach {
                it.date = Utility.getTimeFromString(it.datetimeFormat)
            }
            Collections.sort(model.data.ticketList)
            Collections.reverse(model.data.ticketList)
            atpList.clear()
            atpList.addAll(model.data.ticketList)
            if (!CURR_SCR.equals(HISTORY)) {
                isPending = model.data.isPendingPlan.get(0).activePlans
                tobePlanList.clear()
                model.data.atpList.forEach {
                    val isValid = Utility.checkIsValid(it.datetimeFormat)
                    it.valid_status = isValid
                }
                tobePlanList.addAll(model.data.atpList)
            }
            when (CURR_SCR) {
                PLAN -> performPlan()
                MYPLAN -> showAtp()
                else -> {
                    rcViewProdDes.adapter = adapter
                    adapter.setRecord_bck(atpList)
                    adapter.notifyDataSetChanged()
                }
            }
            //performNewPart()

            bdoList.clear()
            val bdo_one = SelfParticipationResponse().BdoList()
            bdo_one.bdoId = 0
            bdo_one.bdoName = "Select BDO"
            bdoList.add(bdo_one)
            bdoList.addAll(model.data.bdoList)
            spinnerbospinner.text = bdo_one.bdoName
            spinner.updatelist(bdoList)
            if (atpList.size > 0) {
                Collections.sort(atpList)
                Collections.reverse(atpList)
                mess.visibility = View.GONE
                rcViewProdDes.visibility = View.VISIBLE
            } else {
                mess.visibility = View.VISIBLE
                rcViewProdDes.visibility = View.VISIBLE
                if (CURR_SCR.equals(ACTIVEPLAN))
                    mess.text = "No Active Self Participation"
                else if (CURR_SCR.equals(HISTORY))
                    mess.text = "No Self Participation History"
            }
        }
    }

    override fun onClick(view: View) {
        when (view.id) {
            R.id.new_participation -> {
                if (!CURR_SCR.equals(ACTIVEPLAN))
                    performNewPart()
            }
            R.id.history -> {
                if (!CURR_SCR.equals(HISTORY))
                    performHistory()
            }
            R.id.plan -> {
                if (!CURR_SCR.equals(PLAN))
                    performPlan()
            }
            R.id.iCross -> {
                finish()
            }
            R.id.myAtp -> {
                if (!CURR_SCR.equals(MYPLAN))
                    showAtp()
            }
            R.id.filter -> {
                val intent = Intent(this, SPFilterActivity::class.java)
                val b = Bundle()
                b.putString("manager_level", manager_level)
                b.putInt("manager_id", manager_id)
                b.putString("manager_name", manager_name)
                b.putInt("sr_manager_id", sr_manager_id)
                b.putString("sr_manager_name", sr_manager_name)
                intent.putExtra("bundle", b)
                startActivityForResult(intent, 1001)
            }
            R.id.submit -> {
                launchIntent(NewTicketActivity::class.java, true)
            }
            R.id.spinnerbospinner -> {
                if (!spinner.isEnable)
                    spinner.showSpinerDialog()
            }
        }
    }


    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (data != null) {
            val b = data.getBundleExtra("bundle")
            manager_id = b!!.getInt("manager_id")
            manager_level = b.getString("manager_level")!!
            manager_name = b.getString("manager_name")!!
            sr_manager_id = b.getInt("sr_manager_id")
            sr_manager_name = b.getString("sr_manager_name")!!
        }
    }

    /**
     * new Part
     */
    fun performNewPart() {
        CURR_SCR = ACTIVEPLAN
        atpList.clear()
        adapter.notifyDataSetChanged()
        rcViewProdDes.adapter = adapter
        myAtp_active.visibility = View.GONE
        plan_participation_active.visibility = View.GONE
        new_participation_active.visibility = View.VISIBLE
        bdoLayout.visibility = View.VISIBLE
        history_active.visibility = View.GONE
        showProgress()
        val map = HashMap<String, Any>()
        map.put("apiToken", preference.getString("token"))
        if (manager_id == 0)
            map.put("manager_id", "0")
        else
            map.put("manager_id", manager_id.toString())

        if (manager_level.isNullOrEmpty())
            map.put("manager_level", "0")
        else
            map.put("manager_level", manager_level)

        presenter.onRequestData(map)
    }

    fun performHistory() {
        CURR_SCR = HISTORY
        rcViewProdDes.adapter = adapter
        atpList.clear()
        adapter.notifyDataSetChanged()
        adapter.clear()
        myAtp_active.visibility = View.GONE

        new_participation_active.visibility = View.GONE
        history_active.visibility = View.VISIBLE
        bdoLayout.visibility = View.VISIBLE
        plan_participation_active.visibility = View.GONE
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            showProgress()
            presenter.onRequestHistoryData(preference.getString("token"))
        } else {
            makeToast(resources.getString(R.string.no_internet_connection))
        }
    }

    fun performPlan() {
        new_participation_active.visibility = View.GONE
        history_active.visibility = View.GONE
        bdoLayout.visibility = View.GONE
        myAtp_active.visibility = View.GONE
        plan_participation_active.visibility = View.VISIBLE
        if (CURR_SCR.equals(HISTORY)) {
            CURR_SCR = PLAN
            if (CheckConnectionStatus.isInternetAvailable(this)) {
                showProgress()
                var map = HashMap<String, Any>();
                map.put("apiToken", preference.getString("token"))
                if (manager_id == 0)
                    map.put("manager_id", "0")
                else
                    map.put("manager_id", manager_id.toString())
                if (manager_level.isNullOrEmpty())
                    map.put("manager_level", "0")
                else
                    map.put("manager_level", manager_level)
                presenter.onRequestData(map)
            } else {
                makeToast(resources.getString(R.string.no_internet_connection))
            }
        } else {
            CURR_SCR = PLAN
            if (tobePlanList.size > 0) {
                Collections.sort(tobePlanList)
                Collections.reverse(tobePlanList)
                mess.visibility = View.GONE
                rcViewProdDes.visibility = View.VISIBLE
                rcViewProdDes.adapter = planAdapter
                planAdapter.notifyDataSetChanged()
            } else {
                rcViewProdDes.visibility = View.GONE
                mess.visibility = View.VISIBLE
                mess.text = "No Plan SP"
            }
        }
    }

    fun showAtp() {
        new_participation_active.visibility = View.GONE
        history_active.visibility = View.GONE
        bdoLayout.visibility = View.GONE
        plan_participation_active.visibility = View.GONE
        myAtp_active.visibility = View.VISIBLE
        if (CURR_SCR.equals(HISTORY)) {
            plannedList.clear()
            CURR_SCR = MYPLAN
            if (CheckConnectionStatus.isInternetAvailable(this)) {
                showProgress()
                var map = HashMap<String, Any>();
                map.put("apiToken", preference.getString("token"))
                if (manager_id == 0)
                    map.put("manager_id", "0")
                else
                    map.put("manager_id", manager_id.toString())
                if (manager_level.isNullOrEmpty())
                    map.put("manager_level", "0")
                else
                    map.put("manager_level", manager_level)
                presenter.onRequestData(map)
            } else {
                makeToast(resources.getString(R.string.no_internet_connection))
            }
        } else {
            CURR_SCR = MYPLAN
            if (tobePlanList.size > 0) {
                plannedList.clear()
                tobePlanList.forEach {
                    if (it.planAtpStatus == 1 && it.valid_status > 0) {
                        plannedList.add(it)
                    }
                }
                Collections.sort(plannedList)
                Collections.reverse(plannedList)
                mess.visibility = View.GONE
                rcViewProdDes.visibility = View.VISIBLE
                rcViewProdDes.adapter = allreadyAdapter
                allreadyAdapter.notifyDataSetChanged()
            } else {
                rcViewProdDes.visibility = View.GONE
                mess.visibility = View.VISIBLE
                mess.text = "No Active ATP Presented"
            }
        }
    }

    override fun onResume() {
        super.onResume()
        if (CURR_SCR == HISTORY)
            performHistory()
        else
            performNewPart()
    }

    override fun onSelectedPlanAtp(model: AtpList) {
        if (model.planAtpStatus == 0 && model.valid_status == 1) {
            showdialog(model)
        }
    }

    private fun showdialog(model: AtpList) {
        val dialog = Dialog(this)
        dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_add_participation)
        dialog.setCanceledOnTouchOutside(true)
        dialog.setCancelable(false)
        val tvOkay = dialog.findViewById<View>(R.id.tvMeOkay) as Medium
        tvOkay.setOnClickListener {
            if (CheckConnectionStatus.isInternetAvailable(this)) {
                showProgress()
                presenter.planSelfParticipation(
                    preference.getString("token"),
                    model.planAtpId.toString()
                )
            } else {
                makeToast(resources.getString(R.string.no_internet_connection))
            }
            dialog.dismiss()
        }
        val cancel = dialog.findViewById<View>(R.id.cancel) as Medium
        cancel.setOnClickListener {
            // launchIntent(HomeActivity::class.java, true)
            dialog.dismiss()
        }
        dialog.show()
    }

    override fun onPlanParticipationSuccess(model: ParticipationPlannedResponse) {
        tobePlanList.forEach {
            if (model.data.planAtpId.equals(it.planAtpId.toString())) {
                it.planAtpStatus = 1
            }
        }
        planAdapter.notifyDataSetChanged()
        hideProgress()
        makeToast(model.message)
    }

    override fun onPlanParticipationFailure(model: String) {
        makeToast(model)
    }
}
