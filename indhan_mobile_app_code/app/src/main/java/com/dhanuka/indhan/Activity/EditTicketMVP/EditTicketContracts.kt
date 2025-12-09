package com.dhanuka.indhan.Activity.EditTicketMVP

import com.dhanuka.indhan.Activity.EditTicketMVP.Model.EditTicketBean
import com.dhanuka.indhan.Activity.EditTicketMVP.Model.ImageTicketResponse
import com.dhanuka.indhan.MyVillage.MyVillageBean
import okhttp3.RequestBody
import java.util.*

/**
 * Created by Shivam on 3/6/2018.
 */

interface EditTicketContracts {

    interface IEditTicketView {

        fun showProgress()

        fun hideProgress()

        fun setItems(items: EditTicketBean)

        fun setItems(items: String)

        fun onFailure(items: String)

        fun setVillageList(items: MyVillageBean)
    }

    interface EditTicketPresenter {

        fun onSubmitClick(mEditTicketRequest: HashMap<String, Any>)
        fun onSubmitPhoto(
            image: RequestBody,
            tid: RequestBody,
            token: RequestBody,
            fileNAme: String,
            latitude: RequestBody,
            longitude: RequestBody,
            date: RequestBody
        )

        fun onSuccessUpload(response: ImageTicketResponse)
        fun onFailure(response: String)
        fun onVillageList(visit: HashMap<String, Any>)
    }

    interface EditTicketInteractor {

        fun userEditTicket(listener: OnFinishedListener, model: HashMap<String, Any>)

        fun uploadPhoto(
            listener: EditTicketPresenter,
            image: RequestBody,
            tid: RequestBody,
            token: RequestBody,
            fileNAme: String,
            latitude: RequestBody,
            longitude: RequestBody,
            date: RequestBody
        )

        fun villageList(listener: OnFinishedListener, model: HashMap<String, Any>)

        interface OnFinishedListener {
            fun onEditTicketSucess(items: EditTicketBean)

            fun onVillageList(items: MyVillageBean)

            fun onEditTicketFailed(errorMessage: String)
        }
    }
}
