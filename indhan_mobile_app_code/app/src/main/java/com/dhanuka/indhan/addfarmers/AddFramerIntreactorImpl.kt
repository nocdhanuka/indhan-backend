package com.dhanuka.indhan.addfarmers

import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import okhttp3.MultipartBody
import okhttp3.RequestBody
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*

class AddFramerIntreactorImpl : AddFarmerContractor.AddFarmersInteractor {
    override fun onAddFarmers(
        listener: AddFarmerContractor.AddFarmersInteractor.OnFinishedListener,
        model: HashMap<String, RequestBody>,
        part: MultipartBody.Part
    ) {

        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).addFarmer(model, part)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<AddFarmerSuccessModel>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    listener.onAddFarmerFailed(e.localizedMessage ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection.")
                }

                override fun onNext(loginBean: AddFarmerSuccessModel) {
                    listener.onAddFarmerSuccess(loginBean)
                }
            }
            )
    }
}