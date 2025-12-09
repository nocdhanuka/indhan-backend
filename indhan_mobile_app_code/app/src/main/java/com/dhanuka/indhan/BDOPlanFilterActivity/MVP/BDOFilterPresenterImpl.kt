package com.dhanuka.indhan.BDOPlanFilterActivity.MVP

import android.content.Context
import com.dhanuka.indhan.BDOPlanFilterActivity.Model.BDOFilterResponse
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInteractorImpl
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInterfaceFactory

/**
 * Created by John on 6/8/2018.
 */
class BDOFilterPresenterImpl(ctx: Context) : BDOFilterFactory.BDOFilterPresenter {

    var interactor: SyncServiceInterfaceFactory.SyncServiceInteractor
    var home: BDOFilterFactory.BDOFilterView

    init {
        home = ctx as BDOFilterFactory.BDOFilterView
        interactor = SyncServiceInteractorImpl()
    }

    override fun onRequest(token: String) {
        home.showProgress()
        interactor.getBDOFilterList(this, token)
    }

    override fun onSuccess(token: BDOFilterResponse) {
        home.hideProgress()
        home.onSuccess(token)
    }


    override fun onFailure(msg: String) {
        home.hideProgress()
        home.onFailure(msg)

    }

}