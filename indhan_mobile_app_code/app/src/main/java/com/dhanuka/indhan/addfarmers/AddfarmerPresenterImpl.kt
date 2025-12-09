package com.dhanuka.indhan.addfarmers

import okhttp3.MultipartBody
import okhttp3.RequestBody
import java.util.*

class AddfarmerPresenterImpl(var view: AddFarmerContractor.AddFarmerView) :
    AddFarmerContractor.AddFarmerPresenter,
    AddFarmerContractor.AddFarmersInteractor.OnFinishedListener {
//    override fun onAddFarmer(mProfile: HashMap<String, RequestBody>, part: MultipartBody.Part) {
//    }


//    override fun onAddFarmer(apiToken: String, ecn: String, villageId: String, farmerName: String, fatherName: String, contactsNo: String, altContacts: String, lat: String, long: String, imageFile: String, cropId: String, acres: String) {
//
//    }

    private val mIntreactorImpl: AddFramerIntreactorImpl = AddFramerIntreactorImpl()

    override fun onAddFarmer(mProfile: HashMap<String, RequestBody>, part: MultipartBody.Part) {
        view.showProgress()
        mIntreactorImpl.onAddFarmers(this, mProfile, part)

    }

    override fun onAddFarmerSuccess(items: AddFarmerSuccessModel) {
        view.hideProgress()
        view.setItems(items)
    }

    override fun onAddFarmerFailed(errorMessage: String) {
        view.hideProgress()
        view.failure(errorMessage)
    }


}