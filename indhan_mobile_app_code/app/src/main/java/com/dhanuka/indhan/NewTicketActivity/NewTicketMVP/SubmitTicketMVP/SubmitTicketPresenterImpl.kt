package com.dhanuka.indhan.Activity.NewTicketMVP.SubmitTicketMVP

import com.dhanuka.indhan.Activity.NewTicketMVP.SubmitTicketMVP.Model.SubmitTicketBean
import com.dhanuka.indhan.MyVillage.MyVillageBean
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.util.Utility
import com.google.firebase.analytics.FirebaseAnalytics
import java.util.*

/**
 * Created by Shivam on 3/5/2018.
 */

class SubmitTicketPresenterImpl(private val mISubmitTicketView: SubmitTicketContracts.ISubmitTicketView?) :
    SubmitTicketContracts.SubmitTicketPresenter,
    SubmitTicketContracts.SubmitTicketInteractor.OnFinishedListener {


    override fun onVillageList(visit: HashMap<String, Any>) {
        mISubmitTicketView?.showProgress()
        mSubmitTicketInteractor.villageList(this, visit)


    }

    override fun onVillageList(items: MyVillageBean) {
        mISubmitTicketView?.hideProgress()
        mISubmitTicketView?.setVillageList(items)
    }

    private val mSubmitTicketInteractor: SubmitTicketContracts.SubmitTicketInteractor

    init {
        this.mSubmitTicketInteractor = SubmitTicketInteractorImpl()
    }


    override fun onSubmitClick(mSubmitTicketRequest: HashMap<String, Any>) {
        mISubmitTicketView!!.showProgress()
        mSubmitTicketInteractor.userSubmitTicket(this, mSubmitTicketRequest)
    }

    override fun onSubmitTicketSucess(items: SubmitTicketBean) {
        if (mISubmitTicketView != null) {
            mISubmitTicketView.setItems(items)
            mISubmitTicketView.hideProgress()
        }
    }

    override fun onSubmitTicketFailed(errorMessage: String) {
        logEventCustom(errorMessage, "v2/allVillageList")
    }

    private fun logEventCustom(mes: String?, apiName: String) {
        val bundle = HashMap<String, Any>()
        bundle[FirebaseAnalytics.Param.ITEM_ID] = Preference.getInstance().getString("user_id")
        bundle["Api_Name"] = apiName
        if (mes != null) {
            bundle["error"] = mes
        } else {
            bundle["error"] = "unknown error occurred"
        }
        bundle["user_detail"] = Preference.getInstance().getString("user_id")
            .plus("_${Preference.getInstance().getString("ecn")}")
            .plus("_${Preference.getInstance().getString("user_type")}")
            .plus(
                "_${Preference.getInstance().getString("Username")}"
                    .plus("_${Preference.getInstance().getString("designation")}")
            )
        Utility.logEvent("SubmitTicketPresenterImpl", "failed_api_$apiName", bundle)
    }
}
