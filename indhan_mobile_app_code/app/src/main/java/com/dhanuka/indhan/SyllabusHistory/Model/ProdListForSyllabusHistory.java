package com.dhanuka.indhan.SyllabusHistory.Model;

/**
 * Created by John on 5/15/2018.
 */
public class ProdListForSyllabusHistory {
    int productId, noOfDays, noOfBDO;
    String productName, month;
    int submittedNo_ifDays;
    int submittednoOfBDO;

    public int getSubmittednoOfBDO() {
        return submittednoOfBDO;
    }

    public void setSubmittednoOfBDO(int submittednoOfBDO) {
        this.submittednoOfBDO = submittednoOfBDO;
    }

    public int getSubmittedNo_ifDays() {
        return submittedNo_ifDays;
    }

    public void setSubmittedNo_ifDays(int submittedNo_ifDays) {
        this.submittedNo_ifDays = submittedNo_ifDays;
    }

    public int getProductId() {
        return productId;
    }

    public void setProductId(int productId) {
        this.productId = productId;
    }

    public int getNoOfDays() {
        return noOfDays;
    }

    public void setNoOfDays(int noOfDays) {
        this.noOfDays = noOfDays;
    }

    public int getNoOfBDO() {
        return noOfBDO;
    }

    public void setNoOfBDO(int noOfBDO) {
        this.noOfBDO = noOfBDO;
    }

    public String getProductName() {
        return productName;
    }

    public void setProductName(String productName) {
        this.productName = productName;
    }

    public String getMonth() {
        return month;
    }

    public void setMonth(String month) {
        this.month = month;
    }
}
