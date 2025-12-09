package com.dhanuka.indhan.CustomerCare.Presenter

import android.content.Context
import com.dhanuka.indhan.CustomerCare.Model.CCQueriesModel
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInteractorImpl
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInterfaceFactory
import com.dhanuka.indhan.PlanATP.View.CustomerCareFactory

/**
 * Created by John on 6/8/2018.
 */
class CCPresenterImpl(ctx: Context) : CustomerCareFactory.CCPresenter {

    var interactor: SyncServiceInterfaceFactory.SyncServiceInteractor
    var home: CustomerCareFactory.CCHomeInterface

    init {
        home = ctx as CustomerCareFactory.CCHomeInterface
        interactor = SyncServiceInteractorImpl()
    }

    override fun onQueriesSuccess(response: CCQueriesModel) {
        home.hideProgress()
        home.OnSuccess(response)
    }

    override fun onRequestQueries(token: String) {
        home.showProgress()
        interactor.getCCQueries(this, token)
    }

    override fun onFailure(msg: String) {
        home.hideProgress()
        home.onFailure(msg)
    }

}