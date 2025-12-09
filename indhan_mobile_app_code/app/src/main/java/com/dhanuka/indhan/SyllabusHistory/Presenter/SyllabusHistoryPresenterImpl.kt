package com.dhanuka.indhan.SyllabusHistory.Presenter

import android.content.Context
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.DataSyncServices.Model.PlanSyllabusModel
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInteractorImpl
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInterfaceFactory
import com.dhanuka.indhan.Model.ProductSyllabusModel
import com.dhanuka.indhan.PlanSyllabusForBdo.Model.SyllabusApiProduct
import com.dhanuka.indhan.PlanSyllabusForBdo.Model.SyllabusProduct
import com.dhanuka.indhan.util.Utility
import org.greenrobot.eventbus.EventBus
import org.joda.time.DateTime

/**
 * Created by John on 5/29/2018.
 */
class SyllabusHistoryPresenterImpl(ctx: Context) :
    SyncServiceInterfaceFactory.SyllabusHistoryPresenter {
    override fun onRequestDataResultFailure() {

    }

    var interactor: SyncServiceInterfaceFactory.SyncServiceInteractor
    var ctx: Context
    var home: SyncServiceInterfaceFactory.SyllabusHistoryView

    init {
        this.ctx = ctx
        home = ctx as SyncServiceInterfaceFactory.SyllabusHistoryView
        interactor = SyncServiceInteractorImpl()
    }

    override fun onRequest(token: String, syllabus: ArrayList<SyllabusProduct>) {
        home.showProgress()
        val test: ArrayList<SyllabusApiProduct> = ArrayList()
        syllabus.forEach {
            var its = SyllabusApiProduct()
            its.month = Utility.getMonthNo(it.month.toString())

            if (Utility.currentMonthNo() == 12) {
                if (its.month == 12) {
                    its.year = DateTime.now().year
                } else if (its.month == 1) {
                    its.year = DateTime.now().year + 1
                }
            } else {
                its.year = DateTime.now().year
            }
            its.crop_id = it.crop_id
            its.product_id = it.product_id
            its.dd = it.no_of_dd
            its.days = it.no_of_days
            its.bdo_id = it.bdo_id
            its.record_id = it.record_id
            test.add(its)
        }

        val model = ProductSyllabusModel()
        model.apiToken = token
        model.planData = test
        interactor.savePlanActivityData(this, model)


    }

    override fun onPlanSyllabusSuccess(homeBean: PlanSyllabusModel) {
        home.hideProgress()
        if (homeBean.statusCode == 409) {
            EventBus.getDefault().post("logout")
        } else {
            val beanList = homeBean.data!!
            if (beanList.size > 0) {
                beanList.forEach {
                    val product = SyllabusProduct()
                    product.isUploaded = 1
                    product.record_id = it.recordId
                    DatabaseManager.updateUploadStatusinDB(ctx, product)

                }
            }
            home.onPlanSyllabusSuccess(homeBean)
        }
    }
}
