package com.dhanuka.indhan.validatefarmers

import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*

class ValidateFarmerIntractor : ValidateFarmerContractor.TestimonialListInteractor {
    override fun onCallFarmer(
        listener: ValidateFarmerContractor.TestimonialListInteractor.OnFinishedListener,
        mParam: HashMap<String, Any>
    ) {
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).updateCallStatus(mParam)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<SuccessBean>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    listener.onFailed(e.localizedMessage ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection.")
                }

                override fun onNext(villageBean: SuccessBean) {
                    listener.onCallSuccess(villageBean)
                }
            }
            )
    }

    override fun onMakeLead(
        listener: ValidateFarmerContractor.TestimonialListInteractor.OnFinishedListener,
        mLead: HashMap<String, Any>
    ) {

        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).updateLeadStatus(mLead)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<SuccessBean>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    listener.onFailed(e.localizedMessage ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection.")
                }

                override fun onNext(villageBean: SuccessBean) {
                    listener.onStatusChangeSuccess(villageBean)
                }
            }
            )
    }

    override fun getFarmerList(
        listener: ValidateFarmerContractor.TestimonialListInteractor.OnFinishedListener,
        mFarmer: HashMap<String, Any>
    ) {
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).getValidateFarmerList(mFarmer)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<ValidatedFarmerBean>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    listener.onFailed(e.localizedMessage ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection.")
                }

                override fun onNext(villageBean: ValidatedFarmerBean) {
                    listener.onFarmerListSuccess(villageBean)
                }
            }
            )
    }

}