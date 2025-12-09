package com.dhanuka.indhan.EarningFilter

import com.dhanuka.indhan.MyVillage.MyVillageBean
import java.util.*

class EarningFilterPresenter(var view: EarningFilterContractor.EarningFilterView?) :
    EarningFilterContractor.EarningFilterPresenter,
    EarningFilterContractor.EarningFilterInteractor.OnFinishedListener {

    lateinit var mIntractor: EarningFilterContractor.EarningFilterInteractor

    init {
        mIntractor = EarningFilterIntractor()

    }

    override fun onVillageList(mParam: HashMap<String, Any>) {
        view?.showProgress()
        mIntractor.onVillageList(this, mParam)
    }

    override fun onVillageSuccess(items: MyVillageBean) {
        view?.hideProgress()
        view?.setItems(items)

    }

    override fun onVillageFailed(errorMessage: String) {
        view?.hideProgress()
        view?.failure(errorMessage)
    }
}