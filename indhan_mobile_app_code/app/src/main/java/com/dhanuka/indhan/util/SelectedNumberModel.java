package com.dhanuka.indhan.util;

import java.util.ArrayList;

/**
 * Created by Jitender Kumar on 3/29/2018.
 */

public class SelectedNumberModel {

    private ArrayList<String> selectedNumbers;

    public ArrayList<String> getSelectedNumbers() {
        if (selectedNumbers == null) {
            selectedNumbers = new ArrayList<>();
        }
        return selectedNumbers;
    }

    public void setSelectedNumbers(ArrayList<String> selectedNumbers) {
        this.selectedNumbers = selectedNumbers;
    }
}
