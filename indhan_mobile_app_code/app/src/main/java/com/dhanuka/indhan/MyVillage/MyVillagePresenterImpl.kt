package com.dhanuka.indhan.MyVillage

import java.util.*

class MyVillagePresenterImpl(private val view: MyVillageContractor.MyVillageView?) :
    MyVillageContractor.MyVillagePresenter,
    MyVillageContractor.MyVillageInteractor.OnFinishedListener {


    private val myVillageInteractor: MyVillageContractor.MyVillageInteractor

    init {
        this.myVillageInteractor = MyVillageInteractor()
    }

    override fun onVillageSuccess(items: MyVillageBean) {
        view?.setItems(items)
        view?.hideProgress()

    }

    override fun onVillageFailed(errorMessage: String) {
        view?.failure(errorMessage)
        view?.hideProgress()

    }

    override fun onGetMyVillage(mMyVillage: HashMap<String, Any>, isProgress: Boolean) {
        if (isProgress) {
            view?.showProgress()
        }
        myVillageInteractor.ddMyVillage(this, mMyVillage)
    }
}