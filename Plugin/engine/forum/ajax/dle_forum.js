
/*=====================================================
 DLE Forum - by TemplateDleFr
-----------------------------------------------------
 http://dle-files.ru/
-----------------------------------------------------
 File: dle_forum.js
=====================================================
 Copyright (c) 2007,2021 TemplateDleFr
=====================================================
*/

var cache_post = new Array();

// ********************************************************************************
// POST EDIT
// ********************************************************************************
function ajax_post_edit(id)
{
    ShowLoading();
    
    if ( ! cache_post[id] || cache_post[id] == '' )
    {
        cache_post[id] = $('#post-id-' + id).html();
    }
    
    $.post(forum_ajax + 'editpost.php', {id: id, action: 'edit'}, function(data) { $('#post-id-' + id).html(data); });
    
    HideLoading();
    
    return false;
}

function ajax_cancel_post_edit(id)
{
    $('#post-id-' + id).html(cache_post[id]);
    
    return false;
}

// ********************************************************************************
// POST SAVE
// ********************************************************************************
function ajax_save_post_edit(id, forum_wysiwyg)
{
    ShowLoading();
    
    var post_txt = '';
    
    if (forum_wysiwyg == "1")
    {
        post_txt = $('#forum_post_' + id).html();
    }
    else
    {
        post_txt = $('#forum_post_' + id).val();
    }
    
    $.post(forum_ajax + 'editpost.php', {id: id, action: 'save', wysiwyg: forum_wysiwyg, post_text: post_txt}, function(data) { $('#post-id-' + id).html(data); });
    
    cache_post[id] = '';
    
    HideLoading();
    
    return false;
}

// ********************************************************************************
// POST ADD
// ********************************************************************************
function doAddPost()
{
    var form        = document.getElementById('forum-post-form');
    var post_text   = '';
    var editor_mode = '';
    var sec_code    = '';
    
    if (forum_wysiwyg == "1")
    {
        post_text = $('#post_text').html();
        editor_mode = 'wysiwyg';
    }
    else
    {
        post_text = $('#post_text').val();
    }
    
    if (form.sec_code)
    {
        sec_code = form.sec_code.value;
    }
    
    if (post_text == '')
    {
        DLEalert ('Veuillez remplir tous les champs', 'Erreur rencontrée');
        return false;
    }
    
    ShowLoading();
    
    var topic_title = $('#topic_title').val();
    var forum_id    = $('#forum_id').val();
    var topic_id    = $('#topic_id').val();
    var post_id     = $('#post_id').val();
    
    $.post(forum_ajax + 'addpost.php', {post_text: post_text, topic_title: topic_title, forum_id: forum_id, topic_id: topic_id, post_id: post_id, editor_mode: editor_mode, name: form.name.value, mail: form.mail.value, sec_code: sec_code, skin: dle_skin}, function(data) { $('#ajax-post').html(data); $('#post_text').val(''); });
    
    HideLoading();
    
    return false;
}

// ********************************************************************************
// POST DEL
// ********************************************************************************
function postDelete(url) {
    DLEconfirm('Etes-vous sûr de vouloir supprimer ce message?', dle_confirm, function () { document.location = url; } );
};


// ********************************************************************************
// TOPIC DEL
// ********************************************************************************
function topicDelete(url) {
    DLEconfirm('Etes-vous sûr de vouloir supprimer ce sujet?', dle_confirm, function () { document.location = url; } );
};

function rowDelete(url) {
    DLEconfirm('Etes-vous sûr de vouloir supprimer cet enregistrement?', dle_confirm, function () { document.location = url; } );
};

// ********************************************************************************
// TOPIC MENU
// ********************************************************************************
function TopicMenu( tid, forum_url, moderation ){

var menu=new Array();

if (moderation)
{
    menu[0]='<a href="' + forum_url + '&act=moderation&code=calc&tid=' + tid + '">Vers la dernière page</a>';
}

menu[1]='<a href="' + forum_url + '&act=subscription&code=add&tid=' + tid + '">Abonnez-vous au sujet</a>';

menu[2]='<a href="' + forum_url + '&act=_topic&code=forward&tid=' + tid + '">Envoyer à un ami</a>';

menu[3]='<a href="' + forum_url + '&act=_topic&code=print&tid=' + tid + '">Version imprimable</a>';

return menu;
};

// ********************************************************************************
// FORUM MENU
// ********************************************************************************
function ForumMenu( fid, moderation, forum_url ){

var menu=new Array();

if (moderation){

menu[0]='<a href="' + forum_url + '&showforum=' + fid + '&code=hidden">Voir toutes les discussions cachés</a>';

menu[1]='<a href="' + forum_url + '&act=getforum&code=calc&fid=' + fid + '">Convertissez tous les forums</a>';

}

menu[3]='<a href="' + forum_url + '&showforum=' + fid + '&code=today">Sujets actifs</a>';

menu[4]='<a href="' + forum_url + '&showforum=' + fid + '&code=noreply">Sujets sans réponse</a>';

return menu;
};

// ********************************************************************************
// POST EDIT MENU
// ********************************************************************************
function PostEditMenu( pid, forum_url, page, post_n ){

var menu=new Array();

menu[0]='<a onclick="ajax_post_edit(\'' + pid + '\'); return false;" href="#">' + menu_short + '</a>';
menu[1]='<a href="' + forum_url + '&act=post&code=02&pid=' + pid + '&p=' + page + '&pn=' + post_n + '">' + menu_full + '</a>';

return menu;
};

// ********************************************************************************
// USER MENU
// ********************************************************************************
function FUserMenu( url, m_id, group, forum_url ){

var menu=new Array();

	menu[0]='<a href="' + site_dir + 'index.php?subaction=userinfo&user=' + url + '">' + menu_profile + '</a>';

	menu[1]='<a href="' + site_dir + 'index.php?do=pm&doaction=newpm&user=' + m_id + '">' + menu_send + '</a>';
	menu[2]='<a href="' + forum_url + '&act=getforum&code=user&n=' + url + '">Trouver ses sujets</a>';
    menu[3]='<a href="' + forum_url + '&act=posts&user=' + url + '">Rechercher les messages de l\'utilisateur</a>';

	if (group == '1') {
		menu[4]='<a onclick="window.open(\'' + site_dir + dle_admin + '?mod=editusers&action=edituser&id=' + m_id + '\', \'User\',\'toolbar=0,location=0,status=0, left=0, top=0, menubar=0,scrollbars=yes,resizable=0,width=540,height=500\'); return false;" href="#">' + menu_uedit + '</a>';
	}

return menu;
};

// ********************************************************************************
// WARN ADD
// ********************************************************************************
function FWarn(action, user_id, post_id, forum_id)
{
	ShowLoading();
    
    $('#warn-form').remove();

    $('body').append("<div id='warn-form' title='Avertissements' style='display:none'></div>");
    
    $.post(forum_ajax + 'warn.php', {user_id: user_id, action: 'get_form', forum_id: forum_id, post_id: post_id}, function(data)
    {
        $('#warn-form').dialog({
            autoOpen: true, width: 470,
            buttons: {
                "Annuler": function() { $(this).dialog("close"); $("#warn-form").remove(); },
                "Avertir": function()
                {
                    if ($('#cause').val())
                    {
                        ShowLoading();
                        
                        $.post(forum_ajax + 'warn.php', {user_id: user_id, action: action, forum_id: forum_id, post_id: post_id, cause: $('#cause').val(), type: $('#warn_type').val(), time: $('#warn_time').val(), skin: dle_skin}, function(data) {
                            $('#warn-' + post_id).html(data);
                            $(this).dialog("close"); $("#warn-form").remove();
                            HideLoading();
                        });
                    }
                }
            }
        });
        
        $('#warn-form').html(data);
    });
    
    HideLoading();

    return false;
};

// ********************************************************************************
// REPUTATION
// ********************************************************************************
function FRep(action, user_id, post_id, forum_id)
{
	DLEprompt('Entrez la raison du changement de la réputation:', '', 'Réputation', function(response)
    {
        if (response)
        {
            ShowLoading();
            
            $.post(forum_ajax + 'reputation.php', {user_id: user_id, action: action, forum_id: forum_id, post_id: post_id, cause: response, skin: dle_skin}, function(data) { $('#rep-' + post_id).html(data); });
            
            HideLoading();
        }
    });
    
    return false;
};

// ********************************************************************************
// NAVIGATION
// ********************************************************************************
function navigation(pages_count, url)
{
	DLEprompt('', '', 'Aller à la page', function(page)
	{
		if (pages_count >= page)
		{
			window.location.href = url + page;
		}
	});
};

function PostLink(link)
{
	var message = window.location + '#post-' + link;
    
    DLEalert(message, 'Copier le lien direct:');
};

// ********************************************************************************
// SELECT ID
// ********************************************************************************
function select_id( sid )
{	
	var saved = new Array();
	var clean = new Array();
	var add   = 1;
	
	tmp = document.modform.selected_id.value;
	
	if( tmp != "" )
	{
		saved = tmp.split(",");
	}
	
	for( i = 0 ; i < saved.length; i++ )
	{
		if ( saved[i] != "" )
		{
			if ( saved[i] == sid )
			{
				 add = 0;
			}
			else
			{
				clean[clean.length] = saved[i];
			}
		}
	}
	
	if ( add )
	{
		clean[ clean.length ] = sid;
	}
	
	newvalue = clean.join(',');
	
	document.modform.selected_id.value = newvalue;	
};

// ********************************************************************************
// SHOW HIDE
// ********************************************************************************
function ShowHide( name, open ){
	
	if ($('#' + name).is(':hidden') || open)
    {
        $('#' + name).show(1000);
    }
    else
    {
        $('#' + name).hide(1000);
    }
};

// ********************************************************************************
// POST PREVIEW
// ********************************************************************************
function PostPreview()
{
    var post_text   = '';
    var editor_mode = '';
    
    if (forum_wysiwyg == "1")
    {
        post_text   = $('#post_text').html();
        editor_mode = 'wysiwyg';
    }
    else
    {
        post_text = $('#post_text').val();
    }
    
    if (post_text == '')
    {
        DLEalert ( 'Veuillez remplir tous les champs', 'Erreur rencontrée' );
        return false
    }
    ShowLoading();
    
    $.post(forum_ajax + 'post.preview.php', {post_text: post_text, wysiwyg: forum_wysiwyg, skin: dle_skin}, function(data) { $('#post-preview').html(data); });
    
    HideLoading();
    
    return false;
}

// ********************************************************************************
// UPLOADS FORM
// ********************************************************************************
function uploadsform(open_url)
{
	ShowLoading();
    
    $("#uploads-form").remove();

    $("body").append("<div id='uploads-form' title='Telecharger des fichiers' style='display:none'></div>");
    
    $.post(forum_ajax + 'uploads.form.php', {open_url: open_url}, function(data)
    {
        $('#uploads-form').dialog({
            autoOpen: true, width: 470,
            buttons: {
                "Fermer cette fenetre": function() { $(this).dialog("close"); $("#uploads-form").remove(); }
            }
        });
        
        $('#uploads-form').html(data);
    });
    
    HideLoading();

    return false;
};

// ********************************************************************************
// FORUM INS
// ********************************************************************************
function forum_ins(name)
{
	var input = document.getElementById('forum-post-form').post_text;
	var finalhtml = "";
	
	if (forum_wysiwyg !== "1")
	{
		if (dle_txt!= "")
		{
			input.value += dle_txt;
		}
		
		else
		{
			input.value += "[b]"+name+"[/b],"+"\n";
		}
	}
	
	else
	{
		if (dle_txt!= "")
		{
			finalhtml = dle_txt;
		}
		
		else
		{
			finalhtml = "<b>"+name+"</b>,"+"<br />";
		}
		
		tinyMCE.execInstanceCommand('post_text', 'mceInsertContent', false, finalhtml, true);
	}
};

// ********************************************************************************
// CtrlEnter
// ********************************************************************************
function CtrlEnter(event, form)
{
	if((event.ctrlKey) && ((event.keyCode == 0xA)||(event.keyCode == 0xD)))
	{
		form.submit.click();
	}
};

// ********************************************************************************
// Copyright (c) 2014 DLE Files Group
// ********************************************************************************	