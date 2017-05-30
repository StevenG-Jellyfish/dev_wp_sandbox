Summary: Package - {website name}
Name: {website name}
Version: %version
Release: 1
License: Not Applicable
Group: Development/Library
URL: http://jellyfish.co.uk
BuildRoot: %{_tmppath}/%{name}-root
Source0: %{name}-%{version}.tar.gz
Requires: php
BuildArch: noarch

%description
{website name}

%prep
%setup -q

%install
[ "$RPM_BUILD_ROOT" != "/" ] && rm -rf $RPM_BUILD_ROOT

install -d -m 755 $RPM_BUILD_ROOT/home/sites/%{name}
cp -R * $RPM_BUILD_ROOT/home/sites/%{name}

%post
/sbin/service httpd reload

%clean
[ "$RPM_BUILD_ROOT" != "/" ] && rm -rf $RPM_BUILD_ROOT


%files
%defattr(-,apache,apache,-)
/home/sites/%{name}
