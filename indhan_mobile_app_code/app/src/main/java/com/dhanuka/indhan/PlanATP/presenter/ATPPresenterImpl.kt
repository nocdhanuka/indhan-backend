package com.dhanuka.indhan.PlanATP.presenter

import android.content.Context
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.DataBase.TableDataModel.PlanAtpDBModel
import com.dhanuka.indhan.DataSyncServices.Model.PlanAtpSubmitResponseModel
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInteractorImpl
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInterfaceFactory
import com.dhanuka.indhan.Model.PAtpApiModel
import com.dhanuka.indhan.Model.PlanATPApiModel
import org.greenrobot.eventbus.EventBus

/**
 * Created by John on 5/29/2018.
 */
class ATPPresenterImpl(ctx: Context) : ATPPresenter {


    var interactor: SyncServiceInterfaceFactory.SyncServiceInteractor
    var ctx: Context
    var home: ATPPresenter.AtpUI

    init {
        this.ctx = ctx
        home = ctx as ATPPresenter.AtpUI
        interactor = SyncServiceInteractorImpl()
    }

    override fun onRequest(token: String, planAtp: ArrayList<PlanAtpDBModel>) {
        home.showProgress()
        val test: ArrayList<PAtpApiModel> = ArrayList()
        planAtp.forEach {
            val its: PAtpApiModel = PAtpApiModel()
            its.crop_id = it.crop_id
            its.product_id = it.product_id
            its.plan_date = it.date
            its.data_source_id = it.data_source_id
            its.district_id = it.district_id
            its.state_id = it.state_id
            its.language_id = it.languageId
            its.tehsil_id = it.tehsil_id
            its.expected_farmer = it.expected_farmer
            its.expected_pda = it.expected_pda
            its.record_id = it.record_id
            its.presenter_type = it.presenter_type
            its.presenter_id = it.presenter_id
            its.monitor_type = it.monitor_id
            its.monitor_type = it.monitor_type
            its.village = it.village
            its.product_id_2 = it.crop_one.toInt()
            its.product_id_3 = it.crop_two.toInt()
            its.forwhom = it.iS_FL.toInt()
            its.dd_id = it.dD_ID
            test.add(its)
        }
        val model = PlanATPApiModel()
        model.apiToken = token
        model.planATP = test
        interactor.savePlanAtp(this, model)
    }

    override fun onSuccess(homeBean: PlanAtpSubmitResponseModel) {
        home.hideProgress()
        if (homeBean.statusCode == 409) {
            EventBus.getDefault().post("logout")
        } else {
            var beanList = homeBean.data!!
            if (beanList!!.size > 0) {
                beanList.forEach {
                    var product = PlanAtpDBModel()
                    product.isUploaded = 1
                    product.record_id = it.recordId
                    DatabaseManager.updateUploadinATPTable(ctx, product)
                }
            }
            home.onSuccess()
        }
    }

    override fun onRequestDataResultFailure(msg: String) {
        home.onFailureATPshare(msg)
    }
}
