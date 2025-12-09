package com.dhanuka.indhan.addfarmers

import okhttp3.MultipartBody
import okhttp3.RequestBody
import java.util.*

interface AddFarmerContractor {
    interface AddFarmerView {

        fun showProgress()

        fun hideProgress()

        fun setItems(items: AddFarmerSuccessModel)
        fun failure(items: String)
    }

    interface AddFarmerPresenter {

        fun onAddFarmer(mProfile: HashMap<String, RequestBody>, part: MultipartBody.Part)
        //fun onAddFarmer(apiToken:String,ecn:String,villageId:String,farmerName:String,fatherName:String,contactsNo:String,altContacts:String,lat:String,long:String,imageFile:String,cropId:String,acres:String)

    }

    interface AddFarmersInteractor {
        fun onAddFarmers(
            listener: OnFinishedListener,
            model: HashMap<String, RequestBody>,
            part: MultipartBody.Part
        )

        interface OnFinishedListener {
            fun onAddFarmerSuccess(items: AddFarmerSuccessModel)
            fun onAddFarmerFailed(errorMessage: String)
        }
    }
}