package com.dhanuka.indhan.visithistory

import com.dhanuka.indhan.validatefarmers.SuccessBean

class VisitHistoryFarmerPresenter(var view: VisitHistoryFarmerContractor.VisitHistoryFarmerView?) :
    VisitHistoryFarmerContractor.VisitHistoryFarmerPresenter,
    VisitHistoryFarmerContractor.VisitHistoryFarmerInteractor.OnFinishedListener {
    override fun onCallFarmer(mParam: HashMap<String, Any>) {
        view?.showProgress()
        mImtractor.onCallFarmer(this, mParam)
    }

    override fun onMakeLead(mLead: HashMap<String, Any>) {
        view?.showProgress()
        mImtractor.onMakeLead(this, mLead)
    }

    override fun onCallSuccess(items: SuccessBean) {
        view?.hideProgress()
        view?.setCall(items)
    }

    override fun onStatusChangeSuccess(items: SuccessBean) {
        view?.hideProgress()
        view?.setChangeStatus(items)

    }

    private lateinit var mImtractor: VisitHistoryFarmerContractor.VisitHistoryFarmerInteractor

    init {

        mImtractor = VisitHistoryFarmerImtractor()
    }

    override fun onGetVistList(visitParam: HashMap<String, Any>, isShowProgress: Boolean) {
        if (isShowProgress) {
            view?.showProgress()
        }
        mImtractor.onGetVisitList(this, visitParam)

    }

    override fun onVistListSuccess(items: VisitHistoryFarmerBean) {
        view?.hideProgress()
        view?.setItems(items)
    }

    override fun onVisitListFailed(errorMessage: String) {
        view?.hideProgress()
        view?.failure(errorMessage)
    }
}