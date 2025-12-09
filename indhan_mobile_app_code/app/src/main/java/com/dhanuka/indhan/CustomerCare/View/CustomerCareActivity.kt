package com.dhanuka.indhan.PlanATP.View

import android.app.Dialog
import android.graphics.Color
import android.graphics.drawable.ColorDrawable
import android.os.Bundle
import androidx.recyclerview.widget.LinearLayoutManager
import android.view.View
import android.view.Window
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.CustomerCare.Model.CCQueriesModel
import com.dhanuka.indhan.CustomerCare.Presenter.CCPresenterImpl
import com.dhanuka.indhan.Fonts.Medium
import com.dhanuka.indhan.PlanSyllabusForBdo.Adapter.CCFollowUpsAdapter
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.activity_customer_care.*
import org.greenrobot.eventbus.EventBus
import java.util.*
import kotlin.collections.ArrayList

/**
 * Created by John on 7/23/2018.
 */
class CustomerCareActivity : BaseActivity(), CustomerCareFactory.CCHomeInterface,
    CCFollowUpsAdapter.CCListClickListener {


    lateinit var preference: Preference
    lateinit var presenter: CustomerCareFactory.CCPresenter
    lateinit var adapter: CCFollowUpsAdapter
    var userType = ""
    var FOLLOW = "follow"
    var CLOSED = "closed"
    var MSG_NO_CLOSED = "No Closed Queries"
    var MSG_NO_FOLLOW = "No Active/Follow-ups Queries"
    var CURR_STAT = ""
    var responseList: ArrayList<CCQueriesModel.CustomerDatum> = ArrayList()
    var activeList: ArrayList<CCQueriesModel.CustomerDatum> = ArrayList()
    var adapterList: ArrayList<CCQueriesModel.CustomerDatum> = ArrayList()
    var closedList: ArrayList<CCQueriesModel.CustomerDatum> = ArrayList()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_customer_care)
        preference = Preference(this)
        iCross.setOnClickListener(this)
        follow_ups.setOnClickListener(this)
        closed.setOnClickListener(this)
        userType = preference.getString("user_type")
        presenter = CCPresenterImpl(this)
        adapter = CCFollowUpsAdapter(adapterList)
        adapter.setClickListener(this)
        rv_cc.layoutManager =
            LinearLayoutManager(this)
        rv_cc.adapter = adapter
        CURR_STAT = FOLLOW

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
        msg.text = "You have not Participitated in some Tickets, Please provide the reason"
        val tvOkay = dialog.findViewById<View>(R.id.tvMeOkay) as Medium
        tvOkay.setOnClickListener {
            dialog.dismiss()
        }
        dialog.show()
    }

    override fun onClick(view: View) {
        when (view.id) {
            R.id.iCross -> {
                finish()
            }
            R.id.follow_ups -> {
                filterFollowUps()
            }
            R.id.closed -> {
                filterClosed()
            }
        }
    }


    private fun filterFollowUps() {
        adapterList.clear()
        follow_ups_active.visibility = View.VISIBLE
        closed_active.visibility = View.GONE
        CURR_STAT = FOLLOW
        if (activeList.size > 0) {
            rv_cc.visibility = View.VISIBLE
            message_tv.visibility = View.GONE
            adapterList.addAll(activeList)
            adapterList.sort()
            adapterList.reverse()
            adapter.notifyDataSetChanged()
        } else
            showMessageTextView()
    }

    private fun filterClosed() {
        adapterList.clear()
        follow_ups_active.visibility = View.GONE
        closed_active.visibility = View.VISIBLE
        CURR_STAT = CLOSED
        if (closedList.size > 0) {
            rv_cc.visibility = View.VISIBLE
            message_tv.visibility = View.GONE
            adapterList.addAll(closedList)
            adapterList.sort()
            adapterList.reverse()
            adapter.notifyDataSetChanged()
        } else
            showMessageTextView()
    }

    private fun showMessageTextView() {
        rv_cc.visibility = View.GONE
        message_tv.visibility = View.VISIBLE
        if (CURR_STAT.equals(FOLLOW)) {
            follow_ups_active.visibility = View.VISIBLE
            closed_active.visibility = View.GONE
            message_tv.text = MSG_NO_FOLLOW
        } else {
            follow_ups_active.visibility = View.GONE
            closed_active.visibility = View.VISIBLE
            message_tv.text = MSG_NO_CLOSED
        }
    }

    override fun onResume() {
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            presenter.onRequestQueries(preference.getString("token"))
        } else
            makeToast(resources.getString(R.string.no_internet_connection))
        super.onResume()
    }

    override fun OnSuccess(response: CCQueriesModel) {
        if (response != null) {
            if (response.statusCode == 409) {
                EventBus.getDefault().post("logout")
            } else {
                val list = response.data.customerData
                responseList.clear()
                activeList.clear()
                closedList.clear()
                if (list.size > 0) {
                    list.forEach {
                        it.date = Utility.getDateFromCCDateTimeFormat(it.datetimeFormat) as Date?
                        it.date_str = Utility.getDateStringforCC(it.date)
                        it.time = Utility.getTimeStringforCC(it.date)
                    }
                    responseList.addAll(list)
                    responseList.sort()
                    responseList.reverse()
                    responseList.forEach {
                        if (it.status.equals("closed", true)) {
                            closedList.add(it)
                        } else {
                            activeList.add(it)
                        }
                    }
                    adapterList.clear()
                    if (CURR_STAT.equals(FOLLOW)) {
                        filterFollowUps()
                    } else {
                        filterClosed()
                    }
                } else {
                    showMessageTextView()
                }
            }
        }

    }

    override fun onFailure(response: String) {
        makeToast(response)

    }

    override fun onSelected(model: CCQueriesModel.CustomerDatum) {
        if (!model.status.equals("closed", true)) {
            val b = Bundle()
            b.putSerializable("cc_model", model)
            launchIntent(CCQueryDetailActivity::class.java, b, false)
        }
    }
}
