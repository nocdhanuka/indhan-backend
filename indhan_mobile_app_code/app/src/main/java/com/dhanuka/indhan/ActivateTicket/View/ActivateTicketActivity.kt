package com.dhanuka.indhan.PlanATP.View

import android.app.Dialog
import android.graphics.Color
import android.graphics.drawable.ColorDrawable
import android.os.Bundle
import androidx.recyclerview.widget.LinearLayoutManager
import android.view.View
import android.view.Window
import com.dhanuka.indhan.ActivateTicket.MVP.ATPresenter
import com.dhanuka.indhan.ActivateTicket.MVP.ActivateTicketPresenterFactory
import com.dhanuka.indhan.ActivateTicket.Model.ActiveTicketListModel
import com.dhanuka.indhan.ActivateTicket.Model.AtpList
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Fonts.Medium
import com.dhanuka.indhan.NewTicketActivity.NewTicketActivity
import com.dhanuka.indhan.PlanSyllabusForBdo.Adapter.ActivateTicketAdapter
import com.dhanuka.indhan.Preference.Preference

import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.activity_activate_ticket.*

/**
 * Created by John on 5/26/2018.
 */
class ActivateTicketActivity : BaseActivity(), ActivateTicketAdapter.ActivateTicketClickListener,
    ActivateTicketPresenterFactory.ActivityView {

    lateinit var preference: Preference
    lateinit var adapter: ActivateTicketAdapter
    lateinit var presenter: ActivateTicketPresenterFactory.ActivateTicketPresenter
    var atpList: ArrayList<AtpList> = ArrayList()
    var isPending = 0

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_activate_ticket)
        preference = Preference(this)
        submit.text = "Activate New Ticket"
        submit.visibility = View.GONE
        pd_name.text = "Activate Ticket"
        iCross.setOnClickListener(this)
        submit.setOnClickListener(this)
        presenter = ATPresenter(this)
        atpList.clear()
        adapter = ActivateTicketAdapter(atpList, this)
        adapter.setClickListener(this)
        rcViewProdDes.layoutManager =
            LinearLayoutManager(this)
        rcViewProdDes.adapter = adapter
    }

    override fun onResume() {
        super.onResume()
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            presenter.onRequest()
        } else {
            makeToast(resources.getString(R.string.no_internet_connection))
        }
    }

    override fun onClick(view: View) {
        when (view.id) {

            R.id.iCross -> {
                finish()
            }
            R.id.submit -> {
                launchIntent(NewTicketActivity::class.java, true)
            }
        }
    }

    override fun onSelectedActivate(model: AtpList) {
        if (isPending == 1) {
            showdialog()

        } else {
            val b = Bundle()
            b.putSerializable("ticketDetails", model)
            launchIntent(TicketDetailsActivity::class.java, b, false)

        }
    }


    /**
     * List of Active Tickets
     */
    override fun onActivateTicketResponseSucess(token: ActiveTicketListModel) {
        val list = token.data.atpList
        isPending = token.data.isPendingAtp
        atpList.clear()
        atpList.addAll(list)

        if (atpList.size == 0) {
            tv_nodata.visibility = View.VISIBLE
        } else {
            tv_nodata.visibility = View.GONE

            atpList.forEach {
                if (it.planDate.contains("T")) {
                    it.planDate = Utility.getDateStrFromJoda(it.planDate)


                    // it.date=Utility.getDateObj(temp_date)
                }
                it.date = Utility.getDateObj(it.planDate)
                it.isValid = Utility.compareDate(it.date)
            }
            atpList.sort()
            adapter.notifyDataSetChanged()
        }
    }

    override fun onActivateTicketResponseFailure(token: String) {
        if (token.equals("logout")) {
            /* var preference: Preference = Preference.getInstance(this)
             preference.putString("ticketId", "")
             preference.putString("ticketTime", "")
             preference.putBolean("isLogin", false)
             preference.putString("token", "")
             launchIntent(LoginActivity::class.java, true)*/
        } else {
            tv_nodata.visibility = View.VISIBLE

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
        msg.text = "You have not attended some Tickets, Please provide the reason"
        val tvOkay = dialog.findViewById<View>(R.id.tvMeOkay) as Medium
        tvOkay.setOnClickListener {
            dialog.dismiss()
            if (atpList.size > 0) {
                val b = Bundle()
                var list = ArrayList<AtpList>()
                list.clear()
                atpList.forEach {
                    if (it.status == 0 && it.isValid < 0) {
                        list.add(it)
                    }
                }
                b.putSerializable("atpDetails", list)
                b.putInt("isPending", isPending)
                launchIntent(ActiveTicketReasonActivity::class.java, b, false)
                //   launchIntent(SelfParticipationReasonActivity::class.java, b, false)
            } else {
                makeToast("No Tickets Available")
            }
        }
        dialog.show()
    }

}
