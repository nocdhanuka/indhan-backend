package com.dhanuka.indhan.SelfParticipationActivity.SP_MVP

import android.content.Context
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInteractorImpl
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInterfaceFactory
import com.dhanuka.indhan.SelfParticipationActivity.Model.PreviousPlanResponse

class ReasonSPPresenterImpl(context: Context) : SelfParticipationFactory.SPReasonPresenter {
    var listenr: SelfParticipationFactory.SPReasonHomeListener
    var interactor: SyncServiceInterfaceFactory.SyncServiceInteractor

    init {
        listenr = context as SelfParticipationFactory.SPReasonHomeListener
        interactor = SyncServiceInteractorImpl()
    }

    /**
     * Request Previous Data
     */

    override fun onRequestPreviousData(token: String) {
        interactor.getSelfParticipationPreviousData(this, token)
    }

    override fun onPreviousPlanSuccess(message: PreviousPlanResponse) {
        listenr.onSuccess(message)

    }

    override fun onPreviousPlanFailure(message: String) {
        listenr.onFailure(message)

    }


    /**
     * Submit Reason Self Participation
     */
    override fun reasonSP(token: String, atp_it: String, reason: String) {
        var map = HashMap<String, Any>()
        map.put("apiToken", token)
        map.put("atp_id", atp_it)
        map.put("reason", reason)
        interactor.submitSelfParticipationReason(this, map)
    }

    override fun reasonResponse(message: String) {
        listenr.reasonResponse(message)
    }

}