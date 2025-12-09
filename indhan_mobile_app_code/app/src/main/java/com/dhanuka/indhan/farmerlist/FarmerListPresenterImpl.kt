package com.dhanuka.indhan.farmerlist

import java.util.*

class FarmerListPresenterImpl(var view: FarmerListConractor.MyFarmerListView) :
    FarmerListConractor.FarmerListPresenter,
    FarmerListConractor.FarmerListInteractor.OnFinishedListener {

    private var farmerListIntractorImpl: FarmerListConractor.FarmerListInteractor =
        FarmerListIntractorImpl()

    override fun onGetFarmerList(param: HashMap<String, Any>) {

        view.showProgress()
        farmerListIntractorImpl.onFarmerList(this, param)

    }

    override fun onFarmerListSuccess(items: FarmerBean) {
        view.hideProgress()
        view.setItems(items)
    }

    override fun onFarmerListFailed(errorMessage: String) {
        view.hideProgress()
        view.failure(errorMessage)
    }
}