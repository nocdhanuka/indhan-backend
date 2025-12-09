package com.dhanuka.indhan.Activity.LoginMVP

import com.dhanuka.indhan.Activity.LoginMVP.Model.LoginBean
import com.dhanuka.indhan.Retrofit.ApiServiceRequest
import com.dhanuka.indhan.Retrofit.RetrofitClient
import rx.Subscriber
import rx.android.schedulers.AndroidSchedulers
import rx.schedulers.Schedulers
import java.util.*

/**
 * Created by Shivam on 2/26/2018.
 */

class LoginInteractorImpl : LoginContracts.LoginInteractor {

    override fun userLogin(
        listener: LoginContracts.LoginInteractor.OnFinishedListener,
        model: HashMap<String, Any>
    ) {
        RetrofitClient.mvpClient
            .create(ApiServiceRequest::class.java).userLogin(model)
            .subscribeOn(Schedulers.newThread())
            .observeOn(AndroidSchedulers.mainThread())
            .subscribe(object : Subscriber<LoginBean>() {
                override fun onCompleted() {

                }

                override fun onError(e: Throwable) {
                    listener.onLoginFailed(e.localizedMessage ?: "Server or Slow Internet error. Please try again after sometime or with moderate internet connection.")
                }

                override fun onNext(loginBean: LoginBean) {
                    listener.onLoginSucess(loginBean)
                }
            }
            )
    }
}