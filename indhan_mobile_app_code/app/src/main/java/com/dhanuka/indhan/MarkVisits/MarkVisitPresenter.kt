package com.dhanuka.indhan.MarkVisits

import com.dhanuka.indhan.MyVillage.MyVillageBean
import com.dhanuka.indhan.farmerlist.FarmerBean
import okhttp3.MultipartBody
import okhttp3.RequestBody
import java.util.*

class MarkVisitPresenter(val view: MarkVisitContractor.MarkVisitView?) :
    MarkVisitContractor.MarkVisitPresenter,
    MarkVisitContractor.MarkVisitInteractor.OnFinishedListener {


    override fun onFarmerList(visit: HashMap<String, Any>) {
        view?.showProgress()
        markVisitIntractor.framerList(this, visit)
    }

    override fun onFramerList(items: FarmerBean) {
        view?.hideProgress()
        view?.setVillageList(items)
    }

    override fun onVillageList(visit: HashMap<String, Any>) {
        view?.showProgress()
        markVisitIntractor.villageList(this, visit)
    }

    override fun onVillageList(items: MyVillageBean) {
        view?.hideProgress()
        view?.setVillageList(items)
    }

    override fun onMarkVisit(
        markVisitParam: HashMap<String, RequestBody>,
        body: MultipartBody.Part
    ) {
        view?.showProgress()
        markVisitIntractor.markVisit(this, markVisitParam, body)
    }


    var markVisitIntractor: MarkVisitContractor.MarkVisitInteractor = MarkVisitIntractor()


    override fun onMarkVisitSuccess(items: MarkVisitBean) {
        view?.hideProgress()
        view?.setItems(items)

    }

    override fun onMarkVisitFailed(errorMessage: String) {
        view?.hideProgress()
        view?.failure(errorMessage)
    }
}