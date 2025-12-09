package com.dhanuka.indhan.MenuScreenActivity

import android.content.Context
import com.dhanuka.indhan.DataBase.DbStructure.DatabaseManager
import com.dhanuka.indhan.MenuScreenActivity.Factory.MenuScreenFactory
import com.dhanuka.indhan.MenuScreenActivity.Model.MenuDBModel
import java.util.*

/**
 * Created by John on 5/3/2018.
 */
class MenuPresenter(internal var context: Context) : MenuScreenFactory.MenuPresenterListener {

    internal var homeListener: MenuScreenFactory.MenuViewListener
    internal var interactor: MenuScreenFactory.MenuInteractorListener

    init {
        homeListener = context as MenuScreenFactory.MenuViewListener
        interactor = MenuInteractor()
    }

    override fun getMenuFromDBRequest() {
        var menulist: ArrayList<MenuDBModel> = ArrayList()
        menulist = DatabaseManager.getAllMenuList(context)
        getMenuFromDBResultSuccess(menulist)
        //interactor.getMenuFromDB(this)
    }

    override fun getMenuFromDBResultSuccess(menu: ArrayList<MenuDBModel>) {
        //homeListener.onGetMenuFromDBResult(menu)
    }

}
