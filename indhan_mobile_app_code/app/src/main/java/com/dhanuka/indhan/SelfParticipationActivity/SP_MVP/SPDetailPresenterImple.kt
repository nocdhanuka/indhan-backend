/*
package com.dhanuka.indhan.SelfParticipationActivity.SP_MVP

import android.content.Context
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInteractorImpl
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInterfaceFactory
import com.dhanuka.indhan.SelfParticipationActivity.Model.ParticipationPlannedResponse
import com.dhanuka.indhan.SelfParticipationActivity.Model.SelfParticipationResponse

class SPDetailPresenterImple(context: Context) : SelfParticipationFactory.SPDetailPresenter {

    var listenr: SelfParticipationFactory.DetailHomeListener
    var interactor: SyncServiceInterfaceFactory.SyncServiceInteractor

    init {
        listenr = context as SelfParticipationFactory.DetailHomeListener
        interactor = SyncServiceInteractorImpl()
    }


    override fun onGetDetails(token: String, ticketId: String) {
        listenr.showProgress()
        interactor.getSPDeailsData(this, token,ticketId)
    }

    override fun onFailureATPshare(token: String) {
        listenr.hideProgress()
        listenr.onFailureATPshare(token)
    }

    override fun onSuccess(obj: Any) {
        listenr.onSuccess(obj)
    }

}*/
