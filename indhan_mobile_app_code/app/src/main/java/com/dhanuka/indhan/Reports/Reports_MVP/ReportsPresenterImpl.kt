package com.dhanuka.indhan.Reports.Reports_MVP

import android.content.Context
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInteractorImpl
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInterfaceFactory
import com.dhanuka.indhan.Reports.Model.EfficiencyResponse
import com.dhanuka.indhan.Reports.Model.ReportsResponse

class ReportsPresenterImpl(context: Context) : ReportsFactory.ReportsPresenter {


    var listenr: ReportsFactory.ReportsHomeListener
    var interactor: SyncServiceInterfaceFactory.SyncServiceInteractor

    init {
        listenr = context as ReportsFactory.ReportsHomeListener
        interactor = SyncServiceInteractorImpl()
    }

    override fun onRequestReportsData(model: HashMap<String, Any>) {
        listenr.showProgress()
        interactor.getReportsData(this, model)
    }

    override fun onSuccessUpload(response: ReportsResponse) {
        listenr.hideProgress()
        listenr.onSuccess(response)
    }


    override fun onFailure(token: String) {
        listenr.hideProgress()
        listenr.onFailure(token)
    }

    override fun onRequestEfficiency(token: String, date: String) {
        listenr.showProgress()
        interactor.getEfficiencyData(this, token, date)
    }

    override fun onEfficiencySuccess(response: EfficiencyResponse) {
        listenr.hideProgress()
        listenr.onEfficiencySuccess(response)
    }

    override fun onEfficiencyFailure(model: String) {
        listenr.hideProgress()
        listenr.onEfficiencyFailure(model)
    }

}