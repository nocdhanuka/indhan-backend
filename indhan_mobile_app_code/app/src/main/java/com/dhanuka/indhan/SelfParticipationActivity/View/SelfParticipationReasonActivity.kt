package com.dhanuka.indhan.PlanATP.View

import android.app.Dialog
import android.graphics.Color
import android.graphics.drawable.ColorDrawable
import android.os.Bundle
import androidx.recyclerview.widget.LinearLayoutManager
import android.view.View
import android.view.Window
import android.widget.EditText
import com.dhanuka.indhan.Activity.BaseActivity
import com.dhanuka.indhan.Fonts.Medium
import com.dhanuka.indhan.PlanSyllabusForBdo.Adapter.ATPParticipationAdapter
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.SelfParticipationActivity.Model.AtpList
import com.dhanuka.indhan.SelfParticipationActivity.Model.PreviousPlanResponse
import com.dhanuka.indhan.SelfParticipationActivity.SP_MVP.ReasonSPPresenterImpl
import com.dhanuka.indhan.SelfParticipationActivity.SP_MVP.SelfParticipationFactory
import com.dhanuka.indhan.util.CheckConnectionStatus
import kotlinx.android.synthetic.main.activity_self_participation_reason.*

/**
 * Created by John on 5/26/2018.
 */
class SelfParticipationReasonActivity : BaseActivity(),
    ATPParticipationAdapter.ATPParticipationAdapterClickListener,
    SelfParticipationFactory.SPReasonHomeListener {

    lateinit var preference: Preference
    lateinit var adapter: ATPParticipationAdapter
    var atpList = ArrayList<AtpList>()
    lateinit var presenter: SelfParticipationFactory.SPReasonPresenter
    var isPending = 0
    var planAtpId = 0

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_self_participation_reason)
        val bundle = intent.extras
        var model = ArrayList<AtpList>()
        if (bundle != null) {
            model = bundle.getSerializable("atpDetails") as ArrayList<AtpList>
            isPending = bundle.getInt("isPending")
        }
        atpList.addAll(model)
        preference = Preference(this)
        iCross.setOnClickListener(this)
        presenter = ReasonSPPresenterImpl(this)
        adapter = ATPParticipationAdapter(atpList)
        adapter.setClickListener(this)
        rcViewProdDes.layoutManager =
            LinearLayoutManager(this)
        rcViewProdDes.adapter = adapter
        //  if(isPending==2){
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            showProgress()
            presenter.onRequestPreviousData(preference.getString("token"))
        } else {
            makeToast(resources.getString(R.string.no_internet_connection))
        }
        //   }
    }

    override fun onFailure(mes: String) {
        hideProgress()
        if (!mes.equals("Failure"))
            makeToast(mes)
    }


    override fun onClick(view: View) {
        when (view.id) {
            R.id.iCross -> {
                finish()
            }
        }
    }


    override fun onSelectedPlanAtp(model: AtpList) {
        planAtpId = model.planAtpId
        showdialog()

    }


    private fun showdialog() {
        val dialog = Dialog(this)
        dialog.window?.setBackgroundDrawable(ColorDrawable(Color.TRANSPARENT))
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE)
        dialog.setContentView(R.layout.dialog_add_reason)
        dialog.setCanceledOnTouchOutside(false)
        dialog.setCancelable(true)
        val ticketNumber = dialog.findViewById<View>(R.id.reason_tv) as EditText
        val tvOkay = dialog.findViewById<View>(R.id.tvMeOkay) as Medium
        tvOkay.setOnClickListener {
            dialog.dismiss()
            var textd = ticketNumber.text.toString()
            if (textd.isNotEmpty()) {
                proceedSubmittion(textd)
            } else {
                makeToast("Please provide reason.")
            }
        }
        dialog.show()
    }

    override fun onSuccess(model: PreviousPlanResponse) {
        hideProgress()
        if (atpList != null && atpList.size != 0) {
            atpList.clear()
        }

        atpList.addAll(model.data.atpList)
        adapter.notifyDataSetChanged()
    }

    fun proceedSubmittion(reason: String) {
        if (CheckConnectionStatus.isInternetAvailable(this)) {
            showProgress()
            presenter.reasonSP(preference.getString("token"), planAtpId.toString(), reason)
        } else {
            makeToast(resources.getString(R.string.no_internet_connection))
        }
    }

    override fun reasonResponse(msg: String) {
        if (msg.contains("Reason Successfully Updated")) {
            var i = 0
            var returnList = 0
            atpList.forEach {
                if (it.planAtpId == planAtpId) {
                    returnList = i
                }
                i++
            }
            atpList.removeAt(returnList)
            adapter.notifyDataSetChanged()
        }
        planAtpId = 0
        hideProgress()
        makeToast(msg)
    }

}
