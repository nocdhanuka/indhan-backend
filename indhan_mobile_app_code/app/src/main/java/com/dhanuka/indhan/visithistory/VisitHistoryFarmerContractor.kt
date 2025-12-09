package com.dhanuka.indhan.visithistory

import com.dhanuka.indhan.validatefarmers.SuccessBean

interface VisitHistoryFarmerContractor {

    interface VisitHistoryFarmerView {
        fun showProgress()

        fun hideProgress()

        fun setItems(items: VisitHistoryFarmerBean)
        fun failure(items: String)

        fun setCall(items: SuccessBean)
        fun setChangeStatus(items: SuccessBean)
    }


    interface VisitHistoryFarmerPresenter {

        fun onGetVistList(visitParam: HashMap<String, Any>, isShowProgress: Boolean)

        fun onCallFarmer(mParam: HashMap<String, Any>)
        fun onMakeLead(mLead: HashMap<String, Any>)
    }

    interface VisitHistoryFarmerInteractor {

        fun onGetVisitList(listener: OnFinishedListener, model: HashMap<String, Any>)

        fun onCallFarmer(
            listener: VisitHistoryFarmerContractor.VisitHistoryFarmerInteractor.OnFinishedListener,
            mParam: HashMap<String, Any>
        )

        fun onMakeLead(
            listener: VisitHistoryFarmerContractor.VisitHistoryFarmerInteractor.OnFinishedListener,
            mLead: HashMap<String, Any>
        )

        interface OnFinishedListener {
            fun onVistListSuccess(items: VisitHistoryFarmerBean)

            fun onCallSuccess(items: SuccessBean)
            fun onStatusChangeSuccess(items: SuccessBean)

            fun onVisitListFailed(errorMessage: String)
        }
    }
}