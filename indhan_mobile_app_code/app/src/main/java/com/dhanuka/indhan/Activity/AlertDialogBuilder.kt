package com.dhanuka.indhan.Activity

import android.app.AlertDialog
import android.content.Context

class AlertDialogBuilder {

    fun showDefaultDialog(
        context: Context,
        titleText: String,
        messageText: String,
        btnPositiveText: String,
        btnNegativeText: String,
        visibility: Int,
        isCancelable: Boolean,
        dialogButtonListener: ParentActivity.DialogButtonListener
    ) {
        val builder =
            AlertDialog.Builder(context)
        builder.setTitle(titleText)
        builder.setMessage(messageText)
        builder.setPositiveButton(
            btnPositiveText
        ) { arg0, arg1 ->
            dialogButtonListener.onPositiveButton()
            arg0.dismiss()
        }
        builder.setNegativeButton(
            btnNegativeText
        ) { arg0, arg1 ->
            dialogButtonListener.onNegativeButton()
            arg0.dismiss()
        }

        val dialog = builder.create()
        dialog.show()
        dialog.getButton(AlertDialog.BUTTON_NEGATIVE).visibility = visibility
        dialog.setCancelable(isCancelable)
    }

}