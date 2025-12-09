package com.dhanuka.indhan.MenuScreenActivity;

import com.dhanuka.indhan.MenuScreenActivity.Factory.MenuScreenFactory;

/**
 * Created by John on 5/3/2018.
 */
public class MenuInteractor implements MenuScreenFactory.MenuInteractorListener {

    @Override
    public void getMenuFromDB(MenuScreenFactory.MenuPresenterListener listener) {
        //ArrayList<MenuDBModel> dbModel=DatabaseManager.getAllMenuListForChat();
        //listener.getMenuFromDBResultSuccess(dbModel);
    }
}
