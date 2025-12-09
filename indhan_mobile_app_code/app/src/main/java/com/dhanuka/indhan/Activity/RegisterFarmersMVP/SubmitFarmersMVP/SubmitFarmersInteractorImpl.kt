package com.dhanuka.indhan.Activity.RegisterFarmersMVP.SubmitFarmersMVP

import com.dhanuka.indhan.Model.CommonModel
import com.dhanuka.indhan.Model.ContactSyncModel
import com.dhanuka.indhan.Model.RegisterFarmerContactModel
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*

/**
 * Created by Shivam on 3/9/2018.
 */

class SubmitFarmersInteractorImpl : SubmitFarmersContracts.SubmitFarmersInteractor {
    override fun userSubmitFarmersOptimized(
        listener: SubmitFarmersContracts.SubmitFarmersInteractor.OnFinishedListener,
        list: ArrayList<RegisterFarmerContactModel>,
        token: String
    ) {
        val modelData = ContactSyncModel()
        modelData.apiToken = token
        modelData.contactModelList = list
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java)
            .submitNumberOptimized(modelData)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<CommonModel>() {
                override fun onCompleted() {}

                override fun onError(e: Throwable) {
                    listener.onSubmitFarmersFailed(
                        e.localizedMessage
                            ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection."
                    )
                }

                override fun onNext(SubmitFarmersBean: CommonModel) {
                    if (!SubmitFarmersBean.message.isNullOrEmpty())
                        if (SubmitFarmersBean.message.contains("Farmers Successfully Registered!")) {
                            listener.onSubmitFarmersSucess(SubmitFarmersBean)
                        } else {
                            listener.onSubmitFarmersFailed("Server or Slow Internet error. Please try again after sometime or with moderate internet connection.")
                        }
                    else {
                        listener.onSubmitFarmersFailed("Server or Slow Internet error. Please try again after sometime or with moderate internet connection.")
                    }
                }
            })

    }

    override fun userSubmitFarmers(
        listener: SubmitFarmersContracts.SubmitFarmersInteractor.OnFinishedListener,
        model: HashMap<String, Any>
    ) {
    }
}
