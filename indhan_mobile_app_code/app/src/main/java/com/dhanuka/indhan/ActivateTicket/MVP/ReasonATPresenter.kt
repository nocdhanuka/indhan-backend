package com.dhanuka.indhan.ActivateTicket.MVP

import android.content.Context
import com.dhanuka.indhan.ActivateTicket.Model.PendingActiveTicketlistModel
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInteractorImpl
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInterfaceFactory
import com.dhanuka.indhan.Preference.Preference

/**
 * Created by John on 5/31/2018.
 */
class ReasonATPresenter(context: Context) :
    ActivateTicketPresenterFactory.ReasonActivateTicketPresenter {


    val home: ActivateTicketPresenterFactory.ActivityATReasonView
    val interactor: SyncServiceInterfaceFactory.SyncServiceInteractor
    val preference: Preference

    init {
        this.interactor = SyncServiceInteractorImpl()
        preference = Preference(context)
        home = context as ActivateTicketPresenterFactory.ActivityATReasonView
    }

    override fun onRequestPreviousData(token: String) {
        interactor.getActiveTicketPreviousData(this, token)
    }

    override fun onActivateTicketResponseSucess(model: PendingActiveTicketlistModel) {
        home.hideProgress()
        if (model.statusCode == 200) {
            home.onActivateTicketResponseSucess(model)
        } else if (model.statusCode == 409) {
            home.onActivateTicketResponseFailure("logout")
        }
    }

    override fun onActivateTicketResponseFailure(msg: String) {
        home.onActivateTicketResponseFailure(msg)
    }

    override fun reasonATP(token: String, id: String, reason: String) {
        val map = HashMap<String, Any>()
        map.put("apiToken", token)
        map.put("atp_id", id)
        map.put("reason", reason)
        interactor.submitActiveTicketReason(this, map)
    }

    override fun reasonResponse(msg: String) {
        home.reasonResponse(msg)
    }


}