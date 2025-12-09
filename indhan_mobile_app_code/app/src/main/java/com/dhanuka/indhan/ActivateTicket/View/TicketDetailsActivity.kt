package com.dhanuka.indhan.PlanATP.View

import android.app.Dialog
import android.graphics.Color
import android.graphics.drawable.ColorDrawable
import android.os.Bundle
import android.util.Log
import android.view.View
import android.view.Window
import com.dhanuka.indhan.ActivateTicket.MVP.ActivateTicketPresenterFactory
import com.dhanuka.indhan.ActivateTicket.MVP.TicketDetailSubmitPresenterImpl
import com.dhanuka.indhan.ActivateTicket.Model.AtpList
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Activity.LoginActivity
import com.dhanuka.indhan.Activity.NewTicketMVP.SubmitTicketMVP.Model.SubmitTicketBean
import com.dhanuka.indhan.DataBase.TableDataModel.PlanAtpDBModel
import com.dhanuka.indhan.Fonts.Medium
import com.dhanuka.indhan.PlanSyllabusForBdo.Adapter.ActivateTicketAdapter
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.CheckConnectionStatus
import com.dhanuka.indhan.util.Utility
import kotlinx.android.synthetic.main.activity_active_ticket_detail.*
import org.joda.time.DateTime
import org.joda.time.format.DateTimeFormat


/**
 * Created by John on 5/26/2018.
 */
class TicketDetailsActivity : BaseActivity(),
    ActivateTicketPresenterFactory.TicketDetailActivityView {
    lateinit var preference: Preference
    lateinit var adapter: ActivateTicketAdapter
    var atpList: ArrayList<PlanAtpDBModel> = ArrayList()
    lateinit var presenter: ActivateTicketPresenterFactory.TicketDetailPresenter
    lateinit var model: AtpList
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_active_ticket_detail)
        presenter = TicketDetailSubmitPresenterImpl(this)
        val bundle = intent.extras
        if (bundle != null) {
            model = bundle.getSerializable("ticketDetails") as AtpList
        }
        setUpView()
        preference = Preference(this)
        iCross.setOnClickListener(this)
        activateTicketBtn.setOnClickListener(this)
    }

    private var canBePlanned: Boolean = false
    private fun setUpView() {
        if (model.planDate.isNullOrEmpty()) date.text = "- -"
        else {
            if (model.planDate.length > 10) {
                date.text = Utility.getDateForerrorView(model.planDate)
                val plan_date = Utility.getDateStrFromJoda(model.planDate)
                val formatter = DateTimeFormat.forPattern("yyyy-MM-dd")
                //    val formatter =  DateTimeFormat.forPattern("yyyy-MM-dd")
                val ticketDate = formatter.parseDateTime(plan_date)
                val currentDate = formatter.parseDateTime(DateTime.now().toString(formatter))
                canBePlanned = ticketDate == currentDate
                Log.e("vino", "vinod " + canBePlanned + ticketDate + " " + currentDate);
            } else {
                date.text = Utility.getDateForView(model.planDate)
                val formatter = DateTimeFormat.forPattern("yyyy-MM-dd")
                val ticketDate = formatter.parseDateTime(model.planDate)
                val currentDate = formatter.parseDateTime(DateTime.now().toString(formatter))
                canBePlanned = ticketDate == currentDate
                Log.e("vino", "vinod " + canBePlanned + ticketDate + " " + currentDate);
            }
        }

        if (model.language.isNullOrEmpty()) tv_language.text = "- -"
        else tv_language.text = model.language

        if (model.crop.isNullOrEmpty()) tv_crop.text = "- -"
        else tv_crop.text = model.crop

        if (model.product.isNullOrEmpty()) tv_product.text = "- -"
        else tv_product.text = model.product

        if (model.dataSource.isNullOrEmpty()) tv_ds.text = "- -"
        else tv_ds.text = model.dataSource

        if (model.state.isNullOrEmpty()) tv_state.text = "- -"
        else tv_state.text = model.state

        if (model.district.isNullOrEmpty()) tv_district.text = "- -"
        else tv_district.text = model.district

        if (model.tehsil.isNullOrEmpty()) tv_tehsil.text = "- -"
        else tv_tehsil.text = model.tehsil

        if (model.village.isNullOrEmpty()) tv_village.text = "- -"
        else tv_village.text = model.village

        tv_farmer.text = model.expectedFarmer.toString()
        tv_pda.text = model.expectedPda.toString()
//        if(model.status == 1 || !canBePlanned){
//            appBarBottom.alpha = 0.4F
//            activateTicketBtn.isEnabled = false
//            activateTicketBtn.isActivated = false
//        } else{
//            appBarBottom.alpha = 1.0F
//            activateTicketBtn.isEnabled = true
//            activateTicketBtn.isActivated = true
//        }
    }

    override fun onClick(view: View) {
        when (view.id) {
            R.id.iCross -> {
                finish()
            }
            R.id.activateTicketBtn -> {
                if (model != null) {
                    if (CheckConnectionStatus.isInternetAvailable(this)) {
                        val query = HashMap<String, Any>()
                        query.put("apiToken", preference.getString("token"))
                        query.put("plan_atp_id", model.planAtpId.toString())
                        presenter.onActivateTicket(query)
                    } else {
                        makeToast(resources.getString(R.string.no_internet_connection))
                    }
                }
            }
        }
    }

    private fun showdialog(ticketNumberDisp: String?) {
        val dialog = Dialog(this)
        dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_ticket_number)
        dialog.setCanceledOnTouchOutside(false)
        dialog.setCancelable(false)
        val ticketNumber = dialog.findViewById<View>(R.id.tvMeTicketNumber) as Medium
        ticketNumber.text = ticketNumberDisp
        val tvOkay = dialog.findViewById<View>(R.id.tvMeOkay) as Medium
        tvOkay.setOnClickListener {
            launchIntent(ActivateTicketActivity::class.java, true)
            dialog.dismiss()
        }
        dialog.show()
    }

    override fun onActivateTicketResponseSucess(token: SubmitTicketBean) {
        showdialog(token.data)
    }

    override fun onActivateTicketResponseFailure(token: String) {
        if (token.equals("logout")) {
            var preference: Preference = Preference.getInstance(this)
            preference.putString("ticketId", "")
            preference.putString("ticketTime", "")
            preference.putBolean("isLogin", false)
            preference.putString("token", "")
            launchIntent(LoginActivity::class.java, true)
        } else makeToast(token)
    }
}
