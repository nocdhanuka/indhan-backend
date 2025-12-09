package com.dhanuka.indhan.CustomerCare.Presenter

import android.content.Context
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInteractorImpl
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInterfaceFactory
import com.dhanuka.indhan.PlanATP.View.CustomerCareFactory

/**
 * Created by John on 6/8/2018.
 */
class CCQueryDetailPresenterImpl(ctx: Context) : CustomerCareFactory.CCQueryDetailPresenter {

    var interactor: SyncServiceInterfaceFactory.SyncServiceInteractor
    var home: CustomerCareFactory.CCQueryDetailHomeInterface

    init {
        home = ctx as CustomerCareFactory.CCQueryDetailHomeInterface
        interactor = SyncServiceInteractorImpl()
    }

    override fun OnUpdateRemark(token: HashMap<String, Any>) {
        interactor.submitCCRemarks(this, token)
    }

    override fun onUpdateRemarkSuccess(response: String) {
        home.OnSuccess(response)
    }

    override fun onFailure(msg: String) {
        home.hideProgress()
        home.onFailure(msg)
    }

}