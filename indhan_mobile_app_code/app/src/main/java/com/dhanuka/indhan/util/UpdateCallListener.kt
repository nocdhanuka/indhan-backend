package com.dhanuka.indhan.util

interface UpdateCallListener {
    fun onSuccess(isYes: String, farmerId: String, farmerType: String, contactrNo: String)
}