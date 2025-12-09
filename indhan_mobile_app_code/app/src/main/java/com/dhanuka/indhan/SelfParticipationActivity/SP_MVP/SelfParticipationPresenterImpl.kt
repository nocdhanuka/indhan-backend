package com.dhanuka.indhan.SelfParticipationActivity.SP_MVP

import android.content.Context
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInteractorImpl
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInterfaceFactory
import com.dhanuka.indhan.SelfParticipationActivity.Model.ParticipationPlannedResponse
import com.dhanuka.indhan.SelfParticipationActivity.Model.SPDetailModel
import com.dhanuka.indhan.SelfParticipationActivity.Model.SelfParticipationResponse

class SelfParticipationPresenterImpl(context: Context) :
    SelfParticipationFactory.SelfParticipationPresenter {
    override fun onGetDetails(token: String, ticketId: String) {

        interactor.getSPDeailsData(this, token, ticketId)
    }

    override fun onSPDetailSuccess(model: SPDetailModel) {
        listenr.onSPDetailSuccess(model)
    }

    var listenr: SelfParticipationFactory.HomeListener
    var interactor: SyncServiceInterfaceFactory.SyncServiceInteractor

    init {
        listenr = context as SelfParticipationFactory.HomeListener
        interactor = SyncServiceInteractorImpl()
    }

    override fun onRequestHistoryData(token: String) {
        interactor.getSelfParticipationHistoryData(this, token)
    }


    override fun onFailure(token: String) {
        listenr.onFailure(token)
    }


    override fun onRequestData(token: HashMap<String, Any>) {
        interactor.getSelfParticipationData(this, token)
    }

    override fun onRequestDataSuccess(model: SelfParticipationResponse) {
        if (model.data != null)
            listenr.onSuccess(model)
        else
            listenr.onFailure("Data Unavailable!")
    }

    /**
     * Plan Self Participation
     */
    override fun planSelfParticipation(token: String, atp_it: String) {
        var map = HashMap<String, Any>()
        map.put("apiToken", token)
        map.put("atp_id", atp_it)
        interactor.planSelfParticipation(this, map)
    }

    override fun onPlanParticipationSuccess(message: ParticipationPlannedResponse) {
        listenr.onPlanParticipationSuccess(message)
    }

    override fun onPlanParticipationFailure(message: String) {
        listenr.onPlanParticipationFailure(message)

    }

    /**
     * Request Previous Data
     */


}