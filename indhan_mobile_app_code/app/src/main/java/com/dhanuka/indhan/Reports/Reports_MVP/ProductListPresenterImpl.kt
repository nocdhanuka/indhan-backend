package com.dhanuka.indhan.Reports.Reports_MVP

import android.content.Context
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInteractorImpl
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInterfaceFactory
import com.dhanuka.indhan.Reports.Model.ProductListResponse

class ProductListPresenterImpl(context: Context) : ReportsFactory.ProductListPresenter {
    override fun onSuccess(response: ProductListResponse) {
        listenr.hideProgress()
        listenr.onSuccess(response)

    }

    var listenr: ReportsFactory.ProductHomeListener
    var interactor: SyncServiceInterfaceFactory.SyncServiceInteractor

    init {
        listenr = context as ReportsFactory.ProductHomeListener
        interactor = SyncServiceInteractorImpl()
    }

    override fun onRequestProductList(model: String) {
        listenr.showProgress()
        interactor.getProductList(this, model)
    }

    override fun onFailure(token: String) {
        listenr.hideProgress()
        listenr.onFailure(token)
    }

}