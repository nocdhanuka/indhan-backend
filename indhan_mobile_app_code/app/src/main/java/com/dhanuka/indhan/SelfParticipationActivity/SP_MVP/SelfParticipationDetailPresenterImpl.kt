package com.dhanuka.indhan.SelfParticipationActivity.SP_MVP

import com.dhanuka.indhan.Activity.EditTicketMVP.Model.ImageTicketResponse
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInteractorImpl
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInterfaceFactory
import okhttp3.RequestBody

class SelfParticipationDetailPresenterImpl(context: SelfParticipationFactory.SPDetailHomeListener) :
    SelfParticipationFactory.SelfParticipationDetailPresenter {
    var listenr: SelfParticipationFactory.SPDetailHomeListener
    var interactor: SyncServiceInterfaceFactory.SyncServiceInteractor

    init {
        listenr = context
        interactor = SyncServiceInteractorImpl()
    }

    override fun onSubmitPhoto(model: HashMap<String, RequestBody>) {
        listenr.showProgress()
        interactor.submitgetSelfParticipationData(this, model)
    }

    override fun onSubmitPhoto2(model: HashMap<String, RequestBody>) {
        listenr.showProgress()
        interactor.submitgetImageUploadData(this, model)
    }


    override fun onSuccessUpload(response: ImageTicketResponse) {
        listenr.hideProgress()

        if (response.statusCode == 200) {
            listenr.onSuccess(response.data.url)
        } else {
            listenr.onFailure(response.message)
        }
    }

    override fun onSuccessUploadParticipation(response: ImageTicketResponse) {
        listenr.hideProgress()
        if (response.statusCode == 200) {
            listenr.onSuccess(response.data.url)
        } else {
            listenr.onFailureParticipation(response.message)
        }
    }

    override fun onFailure(token: String) {
        listenr.hideProgress()
        listenr.onFailure(token)
    }

    override fun onFailureParticipation(token: String) {
        listenr.hideProgress()
        listenr.onFailureParticipation(token)
    }

}