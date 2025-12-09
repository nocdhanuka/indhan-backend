package com.dhanuka.indhan.MarkVisits

import com.dhanuka.indhan.MyVillage.MyVillageBean
import com.dhanuka.indhan.farmerlist.FarmerBean
import okhttp3.MultipartBody
import okhttp3.RequestBody
import java.util.*

interface MarkVisitContractor {
    interface MarkVisitView {

        fun showProgress()

        fun hideProgress()

        fun setItems(items: MarkVisitBean)

        fun setVillageList(items: MyVillageBean)

        fun setVillageList(items: FarmerBean)

        fun failure(items: String)
    }

    interface MarkVisitPresenter {

        fun onMarkVisit(markVisitParam: HashMap<String, RequestBody>, body: MultipartBody.Part)

        fun onVillageList(visit: HashMap<String, Any>)

        fun onFarmerList(visit: HashMap<String, Any>)
    }

    interface MarkVisitInteractor {

        fun markVisit(
            listener: OnFinishedListener,
            model: HashMap<String, RequestBody>,
            body: MultipartBody.Part
        )

        fun villageList(listener: OnFinishedListener, model: HashMap<String, Any>)

        fun framerList(listener: OnFinishedListener, model: HashMap<String, Any>)


        interface OnFinishedListener {
            fun onMarkVisitSuccess(items: MarkVisitBean)

            fun onFramerList(items: FarmerBean)

            fun onVillageList(items: MyVillageBean)

            fun onMarkVisitFailed(errorMessage: String)
        }
    }
}