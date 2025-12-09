package com.dhanuka.indhan.DataSyncServices

import android.content.Context
import android.text.TextUtils
import com.dhanuka.indhan.Activity.HomeMVP.Model.HomeBean
import com.dhanuka.indhan.Activity.NewTicketMVP.DistrictMVP.DistrictContracts
import com.dhanuka.indhan.Activity.NewTicketMVP.DistrictMVP.Model.DistrictModel
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.DataSourceBean
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.ProductBean
import com.dhanuka.indhan.Activity.NewTicketMVP.Model.StateBean
import com.dhanuka.indhan.Constants.Constants
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.DataBase.TableDataModel.*
import com.dhanuka.indhan.DataSyncServices.Model.MySyllabusModel
import com.dhanuka.indhan.DataSyncServices.Model.PlanActivityDataModel
import com.dhanuka.indhan.DataSyncServices.Model.PlanRequirementDataModel
import com.dhanuka.indhan.DataSyncServices.Model.PlanSyllabusModel
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInteractorImpl
import com.dhanuka.indhan.DataSyncServices.Presenter.SyncServiceInterfaceFactory
import com.dhanuka.indhan.MenuScreenActivity.Model.MenuDBModel
import com.dhanuka.indhan.MenuScreenActivity.dbmodel.CropBean
import com.dhanuka.indhan.PlanSyllabusForBdo.Model.SyllabusProduct
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.SyllabusHistory.Model.SharedSyllabusHistoryModel
import com.dhanuka.indhan.roomdatabase.AppDatabase
import com.dhanuka.indhan.roomdatabase.DatabaseClient
import com.dhanuka.indhan.roomdatabase.model.ApiTimeTableModel
import com.dhanuka.indhan.util.Utility
import io.reactivex.Single
import io.reactivex.android.schedulers.AndroidSchedulers
import io.reactivex.schedulers.Schedulers
import java.text.ParseException
import java.text.SimpleDateFormat
import java.util.*

/**
 * Created by John on 5/21/2018.
 */
class DataSyncManager(var context: Context, listener: SyncServiceInterfaceFactory.FailureListener) :
    SyncServiceInterfaceFactory.SyncDataManagerPresenter,
    DistrictContracts.DistrictInteractor.OnFinishedListener {

    private var appDatabase: AppDatabase = DatabaseClient.getInstance(context).appDatabase

    override fun getPLanBDODetailOfS1Failure(homeBean: String) {
        listener.onFailure(homeBean)
    }

    override fun getPLanBDOListOfS1Success(homeBean: BDOPlanListDBModel) {
        if (homeBean.statusCode == 409) {
            listener.onInvalidAttempt()
        } else {
            val klist = ArrayList<PlanAtpList>()
            if (homeBean.data.currentMonthPlanAtpList.size > 0) {
                homeBean.data.currentMonthPlanAtpList.forEach {
                    val test = PlanAtpList()
                    test.id = it.id
                    test.activites = it.activites
                    test.name = it.name
                    test.month = it.month
                    klist.add(test)
                }
            }
            if (homeBean.data.nextMonthPlanAtpList.size > 0) {
                homeBean.data.nextMonthPlanAtpList.forEach {
                    val test = PlanAtpList()
                    test.id = it.id
                    test.activites = it.activites
                    test.name = it.name
                    test.month = it.month
                    klist.add(test)
                }
            }
            DatabaseManager.deleteAllbdoListData(ctx)
            klist.forEach {
                DatabaseManager.saveBDOPlanS1Data(ctx, it)
            }
            listener.onDBSyncSuccess()
        }
    }

    override fun onPlanSyllabusSuccess(homeBean: PlanSyllabusModel) {

    }

    override fun onDistrictSucess(items: DistrictModel) {

    }

    override fun onDistrictFailed(errorMessage: String) {
    }

    var ctx: Context
    var interactor: SyncServiceInterfaceFactory.SyncServiceInteractor
    var listener: SyncServiceInterfaceFactory.FailureListener
    var preference: Preference

    init {
        this.ctx = context
        this.listener = listener
        interactor = SyncServiceInteractorImpl()
        preference = Preference(ctx)
    }

    /**
     * MenuList Data Fetch API
     */
    fun updateMenuInDb(token: String) {
        interactor.getMenuDataFromApi(this, token)
    }

    fun updateMenuInDb(token: String, fbTkon: String, isMenu: Boolean) {
        interactor.getMenuDataFromApi(this, token, fbTkon, isMenu)
    }

    override fun onRequestDataResultSuccess(homeBean: HomeBean) {
        if (homeBean.status_code == 409) {
            listener.onInvalidAttempt()
        } else {
            Single.fromCallable {
                val preference = Preference(context)
                preference.putString(
                    Constants.VERSION_NAME,
                    if (!TextUtils.isEmpty(homeBean.data?.app_latest_version))
                        homeBean.data?.app_latest_version
                    else
                        "5.33"
                )
                when {
                    (preference.getString("user_type").equals("dhanuka_doctor")) -> {
                        preference.putString(
                            "activity_permission",
                            "${homeBean.data?.activityPermission}"
                        )
                    }
                    else -> {
                        preference.putString("activity_permission", "2")
                    }
                }
                val beanList = homeBean.data!!.menulist
                if (beanList!!.size > 0) {
                    DatabaseManager.deleteAllMenuData(ctx)
                    beanList.forEach {
                        val model = MenuDBModel(
                            it.getmID().toString(),
                            it.item!!,
                            it.tagName!!,
                            it.base_url + it.tag_icon,
                            0,
                            1
                        )
                        DatabaseManager.saveMenuData(ctx, model)
                    }
                }

                homeBean.data?.languagesData?.let {
                    if (it.size > 0) {
                        DatabaseManager.deleteAllLanguageData(ctx)
                        it.forEach {
                            DatabaseManager.saveLanguageData(ctx, it)
                        }
                    }
                }
                listener.onDBSyncSuccess()
            }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

            }.subscribe()
        }
    }

    override fun onRequestDataResultFailure() {
        listener.onFailure("Oop\'s something went wrong, please try again later. ")
    }

    /**
     * Product and Crop Generic List  09/08/2018 //johnn
     */

    fun upDateProductCropList(token: String) {
        interactor.getProdCropList(this, token)
    }

    override fun getProdCropSuccess(homeBean: ProductCropResponse) {
        Single.fromCallable {
            if (homeBean.statusCode == 200) {
                insertCropListInDb(homeBean)
                val beanList = homeBean.data.productCropList
                if (beanList?.size != null) {
                    DatabaseManager.deleteAllProductCropData(ctx)

                    beanList.forEach {
                        DatabaseManager.saveProdCrop(ctx, it)
                    }
                }
            }
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    private fun insertCropListInDb(homeBean: ProductCropResponse) {
        Single.fromCallable {
            val appDatabase = DatabaseClient.getInstance(context).appDatabase
            appDatabase.cropDao().deleteTableData()
            appDatabase.productDao().deleteTableData()
            for (i in homeBean.data.productCropList.indices) {
                val data = homeBean.data.productCropList[i]
                val cropBean = CropBean()
                cropBean.crop = data.crop
                cropBean.crop_id = data.cropId
                appDatabase.cropDao().insert(cropBean)

                val productBean = com.dhanuka.indhan.MenuScreenActivity.dbmodel.ProductBean()
                productBean.crop_id = data.cropId
                productBean.product = data.product
                productBean.product_id = data.productId
                appDatabase.productDao().insert(productBean)
            }
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    /**
     * MySyllabus Data Fetch API
     */
    fun getMySyllabus(value: Int) {
        interactor.getMySyllabusData(this, preference.getString("token"), value)
    }

    override fun onMySyllabusDataResultSuccess(homeBean: MySyllabusModel) {
        if (homeBean.statusCode == 409) {
            listener.onInvalidAttempt()
        } else {
            val beanList = homeBean.data.mySyllabus!!
            if (beanList.size > 0) {
                DatabaseManager.deleteAllMySyllabusData(ctx)
                beanList.forEach {
                    val Syllabus = MySyllabusDBModel()
                    Syllabus.crop_id = it.cropId
                    Syllabus.crop_name = it.crop
                    Syllabus.product_id = it.productId
                    Syllabus.product_name = it.product
                    Syllabus.no_of_days = it.days
                    Syllabus.no_of_dd = it.dd
                    if (it.atp != null)
                        Syllabus.atp = it.atp.toInt()
                    else Syllabus.atp = 0;
                    Syllabus.month = it.month.toInt()
                    Syllabus.year = it.year.toInt()
                    DatabaseManager.saveMySyllabusData(ctx, Syllabus)
                }
            }
            listener.onDBSyncSuccess()
        }
    }

    /**
     * Plan Activity data like Crop , product and BDO List
     */
    fun getPlanActivity(token: String) {
        var time: String
        Single.fromCallable {
            val data = getLastHitTimeData("v3/planSyllabus/{api_token}/{updated_date}")
            time = data?.updatedAt ?: "0"
            interactor.getGetPlanActivityData(this, token, time)
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {
        }.subscribe()
    }

    private fun String.updateTimeOnDb() {
        Single.fromCallable {
            val timeData = getLastHitTimeData(this)
            val sdf = SimpleDateFormat(Constants.DATE_TIME_FORMAT)
            val currentDate = sdf.format(Date())
            if (timeData != null) {
                appDatabase.apiTimeDao().updateTimeInDb(this, currentDate)
            } else {
                val timeDataModel = ApiTimeTableModel()
                timeDataModel.apiName = this
                timeDataModel.updatedAt = currentDate
                appDatabase.apiTimeDao().insert(timeDataModel)
            }
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

    private fun getLastHitTimeData(apiName: String): ApiTimeTableModel? {
        return appDatabase.apiTimeDao().getApiTimeData(apiName)
    }

    override fun onPlanActivityDataResultSuccess(homeBean: PlanActivityDataModel) {
        if (homeBean.statusCode == 409) {
            listener.onInvalidAttempt()
        } else {
            Single.fromCallable {
                if (homeBean.data.bdolist.size > 0) {
//                    DatabaseManager.deleteAllBDOData(ctx)
                    homeBean.data.bdolist.forEach {
                        val model = UserTeamModel()
                        model.username = it.bdoName
                        model.designatioN_NAME = it.designation
                        model.phone = it.bdoMobileNo
                        model.iS_ON_DEPUTATION = 0
                        model.id = it.bdoId
                        model.designatioN_CODE = it.bdoCode
                        DatabaseManager.saveUserTeamTableData(ctx, model)
                    }
                }

//                if (homeBean.data.productlist.size > 0) {
//                    homeBean.data.productlist.forEach {
//                        val bean: ProductBean = ProductBean()
//                        bean.setpID(it.productId)
//                        bean.item = it.product
//                    }
//                }
//                if (homeBean.data.croplist.size > 0) {
//                    homeBean.data.croplist.forEach {
//                        val cropBean: CropModel = CropModel()
//                        cropBean.id = it.cropId
//                        cropBean.cropName = it.crop
//                    }
//                }

                listener.onDBSyncSuccess()
                "v3/planSyllabus/{api_token}/{updated_date}".updateTimeOnDb()
            }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

            }.subscribe()
        }
    }

    fun getPlanRequirment() {
        interactor.getPlanRequirement(this, preference.getString("token"))
    }

    override fun onPlanRequirementSuccess(homeBean: PlanRequirementDataModel) {
        if (homeBean.statusCode == 409) {
            listener.onInvalidAttempt()
        } else {
            Single.fromCallable {
                val cropList = homeBean.data.cropList!!
                val prodList = homeBean.data.productList!!
                val dataSourceList = homeBean.data.dataSourceList!!
                val statelist = homeBean.data.stateList!!
                if (cropList.size > 0) {
                    DatabaseManager.deleteAllCropData(ctx)
                    cropList.forEach {
                        val model = CropModel()
                        model.id = it.pid
                        model.cropName = it.item
                        DatabaseManager.saveCropData(ctx, model)
                    }
                }
                if (prodList.size > 0) {
                    DatabaseManager.deleteAllProductData(ctx)
                    prodList.forEach {
                        val model = ProductBean()
                        model.setpID(it.pid)
                        model.item = it.item
                        DatabaseManager.saveProductData(ctx, model)
                    }
                }
                if (dataSourceList.size > 0) {
                    DatabaseManager.deleteAllDataSourceData(ctx)
                    dataSourceList.forEach {
                        val model = DataSourceBean()
                        model.dsID = it.dsID
                        model.item = it.item
                        DatabaseManager.saveDataSource(ctx, model)
                    }
                }
                if (statelist.size > 0) {
                    DatabaseManager.deleteAllStateData(ctx)
                    statelist.forEach {
                        val model = StateBean()
                        model.setsID(it.sid)
                        model.item = it.item
                        DatabaseManager.saveState(ctx, model)
                    }
                }
                listener.onDBSyncSuccess()
            }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

            }.subscribe()
        }
    }

    fun getBDOPlanListDorS1() {
        val test = preference.getString("token")
        interactor.getPLanBDOListOfS1(this, test)
    }

    fun getBDOPlanListDetailS1(id: Int) {
        var test = preference.getString("token")
        interactor.getPLanBDODetailOfS1(this, test, id)
    }

    override fun getPLanBDODetailOfS1Success(homeBean: BDOPlanListDetailDBModel, id: Int) {
        if (homeBean.statusCode == 409) {
            listener.onInvalidAttempt()
        } else {
            Single.fromCallable {
                if (homeBean.data.planAtpDetail.size > 0) {
                    DatabaseManager.deleteAllbdoDetailDataofBDOID(ctx, id)
                    homeBean.data.planAtpDetail.forEach {
                        DatabaseManager.saveBDOPlanS1DetailData(ctx, it)
                    }
                }
                listener.onDBSyncSuccess()
            }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

            }.subscribe()
        }
    }

    fun getSharedSyllabusHistory(token: String) {
        interactor.getSharedSyllabusData(this, token)
    }

    override fun getSharedSyllabusSuccess(homeBean: SharedSyllabusHistoryModel) {
        Single.fromCallable {
            if (homeBean.statusCode == 200) {
                val beanList = homeBean.data.sharedSyllabusLists
                if (beanList.size > 0) {
                    beanList?.forEach {
                        val product = SyllabusProduct()
                        product.product_id = it.product_id.toInt()
                        product.product_name = it.product_name
                        product.crop_id = it.crop_id.toInt()
                        product.crops_name = it.crops_name
                        product.no_of_days = it.no_of_days.toInt()
                        product.no_of_dd = it.no_of_dd.toInt()
                        product.bdo_id = it.bdo_id.toInt()
                        product.bdo_name = it.bdo_name
                        val month = Utility.getMonth(Integer.valueOf(it.month) - 1)
                        product.month = month
                        val givenDateString = it.timestamp
                        val sdf = SimpleDateFormat("EEEEE, MMMM dd yyyy, hh:mm a", Locale.ENGLISH)
                        try {
                            val mDate = sdf.parse(givenDateString)
                            val timeInMilliseconds = mDate.time
                            product.timestamp = timeInMilliseconds.toString()
                        } catch (e: ParseException) {
                            e.printStackTrace()
                        }
                        val rid = preference.getString("ecn") + it.bdo_id + product.timestamp
                        product.record_id = rid
                        product.isUploaded = 1
                        DatabaseManager.saveSyllabus(ctx, product)
                    }
                }
            }
        }.subscribeOn(Schedulers.io()).observeOn(AndroidSchedulers.mainThread()).onErrorReturn {

        }.subscribe()
    }

}
