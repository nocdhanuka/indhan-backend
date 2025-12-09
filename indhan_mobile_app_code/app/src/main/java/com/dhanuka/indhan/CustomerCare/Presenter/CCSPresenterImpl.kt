package com.dhanuka.indhan.CustomerCare.Presenter

import android.content.Context
import com.dhanuka.indhan.CustomerCare.Model.CCQueriesForS1Model
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInteractorImpl
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInterfaceFactory
import com.dhanuka.indhan.PlanATP.View.CustomerCareFactory

/**
 * Created by John on 6/8/2018.
 */
class CCSPresenterImpl(ctx: Context) : CustomerCareFactory.CCSPresenter {

    var interactor: SyncServiceInterfaceFactory.SyncServiceInteractor
    var home: CustomerCareFactory.CCSHomeInterface

    init {
        home = ctx as CustomerCareFactory.CCSHomeInterface
        interactor = SyncServiceInteractorImpl()
    }

    override fun onQueriesSuccess(response: CCQueriesForS1Model) {
        home.hideProgress()
        home.OnSuccess(response)
    }

    override fun onRequestQueries(token: String) {
        home.showProgress()
        interactor.getCCQueriesforS1(this, token)
    }

    override fun onFailure(msg: String) {
        home.hideProgress()
        home.onFailure(msg)
    }

}