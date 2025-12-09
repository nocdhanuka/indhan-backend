package com.dhanuka.indhan.CustomerCare.Presenter

import android.content.Context
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInteractorImpl
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInterfaceFactory
import com.dhanuka.indhan.PlanATP.View.CustomerCareFactory

/**
 * Created by John on 6/8/2018.
 */
class CCQueryforS1DetailPresenterImpl(ctx: Context) :
    CustomerCareFactory.CCQueryDetailforS1Presenter {
    override fun onUpdateBdoSuccess(response: String) {
        home.hideProgress()
        home.OnSuccess(response)
    }

    override fun OnUpdateBDO(token: HashMap<String, Any>) {
        interactor.submitCCForS1Remarks(this, token)
    }

    var interactor: SyncServiceInterfaceFactory.SyncServiceInteractor
    var home: CustomerCareFactory.CCQueryDetailHomeInterface

    init {
        home = ctx as CustomerCareFactory.CCQueryDetailHomeInterface
        interactor = SyncServiceInteractorImpl()
    }


    override fun onFailure(msg: String) {
        home.hideProgress()
        home.onFailure(msg)
    }

}